<!DOCTYPE html>
<html>
<head>

  <style>
    @font-face {
        font-family: 'kayak-sans-light';
        src: url({{ url('fonts/kayak-sans-light.otf') }}) format('opentype');
        src: url({{ url('fonts/kayak-sans-light.ttf') }}) format('truetype'), 
            url({{ url('fonts/kayak-sans-light.otf') }}) format('opentype'), 
            url({{ url('fonts/kayak-sans-light.woff') }}) format('woff'), 
            url({{ url('fonts/kayak-sans-light.svg') }}) format('svg');
        font-weight: normal;
        font-style: normal;
    }
    html {
        padding: 0;
        margin: 0;
        width: 100%;
        min-height: 100%;
        font-size: 17px;
        font-family: 'kayak-sans-light', Arial, Helvetica, sans-serif;
        color: #2E2F2F;
    }
    body {
        padding: 0;
        margin: 0;
        width: 100%;
        min-height: 100%;
        font-size: 17px;
        font-family: 'kayak-sans-light', Arial, Helvetica, sans-serif;
        color: #2E2F2F;
        background: #FAFAFA;
        line-height: 1.5;
    }
    header.header {
        position: relative;
        width: 100%;
        height: 72px;
        font-family: 'kayak-sans-light', Arial, Helvetica, sans-serif;
        margin-bottom: -24px;
        background: #fff;
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.16), 0 3px 6px rgba(0, 0, 0, 0.23);
    }
    .header__content {
        height: 100%;
        display: flex;
        max-width: 864px;
        margin: 0 auto;
        position: relative;
    }
    .logo {
        display: flex;
        align-items: center;
        position: relative;
        padding: 8px 16px;
        flex: 0 0 auto;
        width: 144px;
    }
    .logo__icon {
        color: #00A651;
        display: flex;
        width: 100%;
        padding-left: 48px;
        height: 56px;
        overflow: hidden;
        margin-right: 16px;
        align-items: center;
        text-decoration: none;
        background: url({{ url('images/logo.svg') }}) left center no-repeat;
        background-size: 128px 128px;
    }
    .menu {
        height: 72px;
        width: 230px;
        position: absolute;
        right: 0;
    }
    .menu__nav {
        list-style: none;
        line-height: 56px;
        padding: 0;
        margin: 0;
        background: #FFF;
        z-index: 0;
        position: relative;
        height: auto;
        width: auto;
    }
    .menu__nav-item-container {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .menu__nav-item {
        display: inline-block;
        margin-right: 16px;
    }
    .menu__nav-item a {
        color: #2E2F2F;
        font-size: 16px;
        width: 100%;
        display: block;
        padding: 8px;
        text-decoration: none;
    }
    .menu__nav a:hover,
    .menu__nav a:focus {
        color: #0EA5DA;
    }
    .page-content {
        position: relative;
        outline: none;
        padding: 8px;
        margin: 48px auto;
        max-width: 830px;
        min-height: 48vh;
    }
    .page-content h2 {
        padding-top: 8px;
        font-family: 'kayak-sans-light', Arial, Helvetica, sans-serif;
        font-size: 18px;
        color: #2E2F2F;
    }
    .page-content ul.clean {
        list-style: none;
    }
    .page-content a {
        color: #00A651;
    }
    footer.footer {
        position: relative;
        margin-top: -20px;
        width: 100%;
        height: 56px;
        color: #FFF;
        background: #105263;
        text-align: center;
    }
    .footer__content {
        position: relative;
        width: 100%;
        max-width: 864px;
        margin: 0 auto;
        padding: 0 8px;
        font-size: 14px;
        line-height: 56px;
    }
    .footer__content p {
        margin: 0 16px 0 0;
    }

  </style>
</head>
<body>

  <header class="header">
    <div class="header__content">
      <div class="logo">
        <a href="https://submissions.nasta.tv" class="logo__icon" title="Go to the submissions website."></a>
      </div>

      <div class="menu">
        <div class="menu__nav">
          <ul class="menu__nav-item-container">
            <li class="menu__nav-item"><a rel="noopener" target="_blank" href="https://awards.nasta.tv">Awards</a></li>
            <li class="menu__nav-item"><a rel="noopener" target="_blank" href="https://submissions.nasta.tv">Submissions</a></li>
          </ul>
        </div>
      </div>
    </div>
  </header>

  <section class="page-content">

    @yield('content')

  </section>
  
  <footer class="footer">
    <div class="footer__content" role="presentation">
        <p>&copy; The NaSTA Conference and Awards Weekend 2017</p>
    </div>
  </footer>

</body>
</html>