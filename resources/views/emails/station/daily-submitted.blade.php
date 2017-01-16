<!DOCTYPE html>
<html style="padding: 0;margin: 0;width: 100%;min-height: 100%;font-size: 17px;font-family: 'kayak-sans-light', Arial, Helvetica, sans-serif;color: #2E2F2F;">
<head>

  <style>
      @font-face {
      font-family: 'kayak-sans-light';
      src: url(https://awards.nasta.tv/assets/fonts/kayak-sans-light.otf) format('opentype');
      src: url(https://awards.nasta.tv/assets/fonts/kayak-sans-light.ttf) format('truetype'),
          url(https://awards.nasta.tv/assets/fonts/kayak-sans-light.otf) format('opentype'),
          url(https://awards.nasta.tv/assets/fonts/kayak-sans-light.woff) format('woff'),
          url(https://awards.nasta.tv/assets/fonts/kayak-sans-light.svg) format('svg');
      font-weight: normal;
      font-style: normal;
    }

      .menu__nav a:hover,
      .menu__nav a:focus {
        color: #0EA5DA;
      }
  </style>
</head>
<body style="padding: 0;margin: 0;width: 100%;min-height: 100%;font-size: 17px;font-family: 'kayak-sans-light', Arial, Helvetica, sans-serif;color: #2E2F2F;background: #FAFAFA;line-height: 1.5;">

  <header class="header" style="position: relative;width: 100%;height: 72px;font-family: 'kayak-sans-light', Arial, Helvetica, sans-serif;margin-bottom: -24px;background: #fff;box-shadow: 0 3px 6px rgba(0,0,0,0.16), 0 3px 6px rgba(0,0,0,0.23);">
    <div class="header__content" style="height: 100%;display: flex;max-width: 864px;margin: 0 auto;position: relative;">
      <div class="logo"  style="display: flex;align-items: center;position: relative;padding: 8px 16px;flex: 0 0 auto;width: 144px;">
        <a href="https://submissions.nasta.tv" class="logo__icon" title="Go to the submissions website." style="color: #00A651;display: flex;width: 100%;padding-left: 48px;height: 56px;overflow: hidden;margin-right: 16px;align-items: center;text-decoration: none;background: url(https://awards.nasta.tv/assets/images/header/logo.svg) left center no-repeat;background-size: 128px 128px;"></a>
      </div>

      <div class="menu" style="height: 72px;width: 230px;position: absolute;right: 0;">
        <div class="menu__nav" style="list-style: none;line-height: 56px;padding: 0;margin: 0;background: #FFF;z-index: 0;position: relative;height: auto;width: auto;">
          <ul class="menu__nav-item-container" style="list-style: none;padding: 0;margin: 0;">
            <li class="menu__nav-item" style="display: inline-block;margin-right: 16px;"><a rel="noopener" target="_blank" href="https://awards.nasta.tv" style="color: #2E2F2F;font-size: 16px;width: 100%;display: block;padding: 8px;text-decoration: none;">Awards</a></li>
            <li class="menu__nav-item" style="display: inline-block;margin-right: 16px;"><a rel="noopener" target="_blank" href="https://submissions.nasta.tv" style="color: #2E2F2F;font-size: 16px;width: 100%;display: block;padding: 8px;text-decoration: none;">Submissions</a></li>
          </ul>
        </div>
      </div>
    </div>
  </header>

  <section class="page-content" style="position: relative;outline: none;padding: 8px;margin: 48px auto;max-width: 830px;min-height: 48vh;">

  <h2 style="padding-top: 8px;font-family: 'kayak-sans-light', Arial, Helvetica, sans-serif;font-size: 18px;color: #2E2F2F;">Hi {{ $user->name }},</h2>

  @if (count($entries) == 0)
  <p>You did not enter any of the categories that close today.</p>
  @else
  <p>Your summary of todays deadlines:</p>
  @endif

  <ul style="list-style: none;">
  @foreach ($categories as $cat)
  <li>
  <?php
    echo $cat->name;
    echo ": ";

    if (isset($entries[$cat->id])) {
      $entry = $entries[$cat->id];
      echo $entry->name;
      echo " - ";
      echo $entry->uploadedFiles()->count();
      echo " file(s)";

      if ($entry->isLate())
        echo " - <span style='color: #CF5252'>(This entry was submitted late)</span>";

    } else {
      echo "No entry";
    }

  ?>
  </li>
  @endforeach
  </ul>

    <p>You can view your full list of entries <a href="{{ route('station.categories') }}" style="color: #00A651;">here</a>.</p>

    <h2 style="padding-top: 8px;font-family: 'kayak-sans-light', Arial, Helvetica, sans-serif;font-size: 18px;color: #2E2F2F;">
      Regards,
      <br/ ><br/ >
      The Submissions Team
    </h2>

  </section>
  
  <footer class="footer" style="position: relative;margin-top: -20px;width: 100%;height: 56px;color: #FFF;background: #105263;text-align: center;">
    <div class="footer__content" role="presentation" style="position: relative;width: 100%;max-width: 864px;margin: 0 auto;padding: 0 8px;font-size: 14px;line-height: 56px;">
        <p style="margin: 0 16px 0 0;">&copy; The NaSTA Conference and Awards Weekend 2017</p>
    </div>
  </footer>

</body>
</html>



