<?php
namespace Tests\Helpers\Judge;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use TestCase;

use App\Database\Category\Category;
use App\Database\Category\CategoryResult;
use App\Database\Entry\Entry;
use App\Database\Entry\EntryResult;
use App\Database\Entry\EntryRuleBreak;

use App\Helpers\Judge\DashboardFinalizeHelper;


class DashboardFinalizeHelperTest extends TestCase
{
  use DatabaseTransactions;


  private function createEntryResult($entryId, $score){
    return EntryResult::create([
      'entry_id' => $entryId,
      'score' => $score,
      'feedback' => 'Some initial feedback text',
    ]);
  }

  public function testValidEntries(){
    $catId = "animation";

    $rule = EntryRuleBreak::where('entry_id', 75)->first();
    $rule->result = "accepted";
    $rule->save();

    $this->createEntryResult(75, 10);

    $helper = new DashboardFinalizeHelper(Category::find($catId));
    $this->assertEquals(1, $helper->getMissingResultCount());

    // now define second one
    $this->createEntryResult(29, 8);
    $helper = new DashboardFinalizeHelper(Category::find($catId));
    $this->assertEquals(0, $helper->getMissingResultCount());

    $this->assertEquals(1, $helper->getValidEntries()->count());

    // make the second entry valid
    $entry = Entry::find(75);
    $entry->submitted = true;
    $entry->save();
    $helper = new DashboardFinalizeHelper(Category::find($catId));
    $this->assertEquals(2, $helper->getValidEntries()->count());

    $this->assertEquals(2, $helper->getSortedEntries()->count());
  }

  public function testHasInvalidScores(){
    $catId = "animation";

    $rule = EntryRuleBreak::where('entry_id', 75)->first();
    $rule->result = "accepted";
    $rule->save();

    // make the second entry valid
    $entry = Entry::find(75);
    $entry->submitted = true;
    $entry->save();

    $this->createEntryResult(75, 10);
    $res = $this->createEntryResult(29, 10);

    $helper = new DashboardFinalizeHelper(Category::find($catId));
    $this->assertTrue($helper->hasInvalidScores());

    $res->score = 19;
    $res->save();

    $helper = new DashboardFinalizeHelper(Category::find($catId));
    $this->assertFalse($helper->hasInvalidScores());
  }

  public function testGetEntryLive(){
    $catId = "animation";

    $rule = EntryRuleBreak::where('entry_id', 75)->first();
    $rule->result = "accepted";
    $rule->save();

    // make the second entry valid
    $entry = Entry::find(75);
    $entry->submitted = true;
    $entry->save();

    $this->createEntryResult(75, 10);

    $helper = new DashboardFinalizeHelper(Category::find($catId));
    $this->assertNotNull($helper->getEntry(0));
    $this->assertNull($helper->getEntry(1));

    // fail due to duplicate score
    $res = $this->createEntryResult(29, 10);
    $helper = new DashboardFinalizeHelper(Category::find($catId));
    $this->assertNotNull($helper->getEntry(0));
    $this->assertNull($helper->getEntry(1));

    $res->score = 19;
    $res->save();
    $helper = new DashboardFinalizeHelper(Category::find($catId));
    $this->assertNotNull($helper->getEntry(0));
    $this->assertNotNull($helper->getEntry(1));
  }

  public function testGetEntryStashed(){
    $catId = "animation";

    $rule = EntryRuleBreak::where('entry_id', 75)->first();
    $rule->result = "accepted";
    $rule->save();

    // make the second entry valid
    $entry = Entry::find(75);
    $entry->submitted = true;
    $entry->save();

    $res = CategoryResult::create([
      'category_id' => $catId,
      'winner_id' => 72
    ]);

    $helper = new DashboardFinalizeHelper(Category::find($catId));
    $this->assertNotNull($helper->getEntry(0));
    $this->assertNull($helper->getEntry(1));

    // save a commended
    $res->commended_id = 74;
    $res->save();
    $helper = new DashboardFinalizeHelper(Category::find($catId));
    $this->assertNotNull($helper->getEntry(0));
    $this->assertNotNull($helper->getEntry(1));

    // clear savedentries
    $res->commended_id = null;
    $res->winner_id = null;
    $res->save();
    $helper = new DashboardFinalizeHelper(Category::find($catId));
    $this->assertNull($helper->getEntry(0));
    $this->assertNull($helper->getEntry(1));
  }

}