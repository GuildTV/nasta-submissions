<?php $helper = new \App\Helpers\Judge\DashboardFinalizeHelper($category); ?>

<h3>Final results</h3>

<div class="row">
  <div class="col-md-12">
    <form class="form-horizontal finalizeform" id="finalize-{{ $category->id }}" onsubmit="window.JudgeDashboard.PromptSave(this);return false">

      <input type="hidden" id="category_id" value="{{ $category->id }}" />

      @if ($helper->getMissingResultCount() > 0)
        <p>{{ $helper->getMissingResultCount() }} entries have not been scored yet. You must give all entries scores before they can finalized</p>

      @elseif ($helper->hasInvalidScores())
        <p>Invalid score selections. Please ensure that the two highest scores are used only once.</p>
      @else

        @include('judge.dashboard.comment', [
          'entry' => $helper->getEntry(0),
          'prefix' => 'winner',
          'title' => 'Winner',
        ])

        @include('judge.dashboard.comment', [
          'entry' => $helper->getEntry(1),
          'prefix' => 'commended',
          'title' => 'Highly Commended',
        ])

        <hr />

        @if (!$adminVersion && !$category->isResultsReadOnly())
        <div class="form-group">
          <div class="col-sm-10 col-sm-offset-2">
            <button class="btn btn-primary" type="submit">Finalize results</button>
          </div>
        </div>
        @endif

      @endif

    </form>
  </div>
</div>