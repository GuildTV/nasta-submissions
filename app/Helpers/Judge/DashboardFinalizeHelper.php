<?php 
namespace App\Helpers\Judge;

use App\Database\Category\Category;

use App\Exceptions\InvalidArgumentException;

class DashboardFinalizeHelper {
  private $category;

  private $validEntries;
  private $sortedEntries;

  public function __construct(Category $category){
    $this->category = $category;

    if ($category == null)
      throw new InvalidArgumentException("Invalid category");
  }

  public function getMissingResultCount(){
    return $this->category->entries->filter(function($v){ 
      return $v->canBeJudged(true) && $v->result == null;
    })->count();
  }

  public function getValidEntries(){
    if ($this->validEntries != null)
      return $this->validEntries;

    return $this->validEntries = $this->category->entries
      ->filter(function($v){ return $v->canBeJudged() && $v->result != null; });
  }

  public function getSortedEntries(){
    if ($this->sortedEntries != null)
      return $this->sortedEntries;
    
    return $this->sortedEntries = $this->getValidEntries()
      ->sortByDesc(function($v){ return $v->result->score; })
      ->groupBy(function($v){ return $v->result->score; })
      ->values();
  }

  public function hasInvalidScores(){
    if ($this->getSortedEntries()->count() >= 1 && $this->getSortedEntries()[0]->count() != 1)
      return true;

    if ($this->getSortedEntries()->count() >= 2 && $this->getSortedEntries()[1]->count() != 1)
      return true;

    return false;
  }
  
  public function getEntry($pos){ // $pos = 0 or 1
    if ($pos != 0 && $pos != 1)
      return null;

    if ($this->category->result != null)
      return $pos == 0 ? $this->category->result->winner : $this->category->result->commended;

    if ($this->getValidEntries()->count() <= $pos || $this->getSortedEntries()->count() <= $pos)
      return null;

    return $this->getSortedEntries()[$pos][0];
  }

}