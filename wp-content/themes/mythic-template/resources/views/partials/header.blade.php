<header class="site-header">
  @if(!empty($promotion_line_data))
    @include('template-parts.promotions.promotion-line', ['promotion_text' => $promotion_line_data])
  @endif
  <div class="lg-container">
    <div class="header-wrapper navbar-expand-lg">
      <div class="header-logo">
        <a href="/">
          <img src="assets/images/logo.png" alt="Logo GM">
        </a>
      </div>
      <div class="header-logo-mobile">
        <a href="/">
          <img src="assets/images/logo-mobile.svg" alt="Logo GM">
        </a>
      </div>
      <div id="header-search" class="header-search">
                    <span class="main-search-icon">
                        <i class="icon-search"></i>
                    </span>
        <span id="close-autocomplete" class="close-autocomplete">&#x2715</span>
        <input type="search" class="main-search-field" name="header-search" id="header-search-input"
               placeholder="Search for our Staff Brands of Artists">
        <div id="search-autocomplete" class="autocomplete-container">
          <ul class="autocomplete-list">
            <li>
              <a href="#">
                item 1 <span class="item-category">in Category</span>
              </a>
            </li>
            <li>
              <a href="#">
                item 1 <span class="item-category">in Category</span>
              </a>
            </li>
            <li>
              <a href="#">
                item 1 <span class="item-category">in Category</span>
              </a>
            </li>
            <li>
              <a href="#">
                item 1 <span class="item-category">in Category</span>
              </a>
            </li>
            <li>
              <a href="#">
                item 1 <span class="item-category">in Category</span>
              </a>
            </li>
            <li>
              <a href="#">
                item 1 <span class="item-category">in Category</span>
              </a>
            </li>
            <li>
              <a href="#">
                item 1 <span class="item-category">in Category</span>
              </a>
            </li>
          </ul>
        </div>
      </div>
      <div class="mobile-menu-toggler">
        <div class="burger collapsed" data-bs-toggle="collapse" data-bs-target="#main-navbar"
             aria-controls="main-navbar" aria-expanded="false" aria-label="Toggle navigation">
          <i class="icon-hamburger"></i>
        </div>
      </div>
      <nav class="header-nav collapse navbar-collapse" id="main-navbar">
        <ul class="menu">
          <li class="menu-item">
            <a href="#" class="menu-link">
              Feed
            </a>
          </li>
          <li class="menu-item menu-item-has-mega-menu">
            <a href="#" class="menu-link">
              Shop
            </a>
            <div class="mega-menu-container">
              <div class="lg-container">
                <div class="row">
                  <div class="col-md-2">
                    <div class="mega-menu-widget">
                      <h4 class="widget-title">Lorem ipsum</h4>
                      <ul class="sub-menu">
                        <li>
                          <a href="#">Lorem ipsum</a>
                        </li>
                        <li>
                          <a href="#">Lorem ipsum</a>
                        </li>
                        <li>
                          <a href="#">Lorem ipsum</a>
                        </li>
                      </ul>
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="mega-menu-widget">
                      <h4 class="widget-title">Lorem ipsum</h4>
                      <ul class="sub-menu">
                        <li>
                          <a href="#">Lorem ipsum</a>
                        </li>
                        <li>
                          <a href="#">Lorem ipsum</a>
                        </li>
                        <li>
                          <a href="#">Lorem ipsum</a>
                        </li>
                      </ul>
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="mega-menu-widget">
                      <h4 class="widget-title">Lorem ipsum</h4>
                      <ul class="sub-menu">
                        <li>
                          <a href="#">Lorem ipsum</a>
                        </li>
                        <li>
                          <a href="#">Lorem ipsum</a>
                        </li>
                        <li>
                          <a href="#">Lorem ipsum</a>
                        </li>
                      </ul>
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="mega-menu-widget">
                      <h4 class="widget-title">Lorem ipsum</h4>
                      <ul class="sub-menu">
                        <li>
                          <a href="#">Lorem ipsum</a>
                        </li>
                        <li>
                          <a href="#">Lorem ipsum</a>
                        </li>
                        <li>
                          <a href="#">Lorem ipsum</a>
                        </li>
                      </ul>
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="mega-menu-widget">
                      <h4 class="widget-title">Lorem ipsum</h4>
                      <ul class="sub-menu">
                        <li>
                          <a href="#">Lorem ipsum</a>
                        </li>
                        <li>
                          <a href="#">Lorem ipsum</a>
                        </li>
                        <li>
                          <a href="#">Lorem ipsum</a>
                        </li>
                      </ul>
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="mega-menu-widget">
                      <h4 class="widget-title">Lorem ipsum</h4>
                      <ul class="sub-menu">
                        <li>
                          <a href="#">Lorem ipsum</a>
                        </li>
                        <li>
                          <a href="#">Lorem ipsum</a>
                        </li>
                        <li>
                          <a href="#">Lorem ipsum</a>
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </li>
          <li class="menu-item">
            <a href="#" class="menu-link">
              About us
            </a>
          </li>
          <li class="menu-item menu-item-aside">
            <a href="#" class="menu-link">
              Become a Creator
            </a>
          </li>
          <li class="menu-item display-mobile">
            <a href="#" class="menu-link">
              Account
            </a>
          </li>
        </ul>
        <div class="mobile-currency display-mobile">
          <div class="currensy">USD</div>
        </div>
      </nav>

      <div class="header-actions-box">
        <a href="#" class="action-link action-account-link">
          <i class="icon-user"></i>
        </a>
        <a href="#" class="action-link action-cart-link">
          <i class="icon-cart"></i>
        </a>
        <a href="#" class="action-link action-currency">
          <span class="currency">usd</span>
        </a>
      </div>
    </div>
  </div>
</header>
