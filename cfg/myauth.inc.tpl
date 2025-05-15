<?php
define("USE_PROD", false);

if (USE_PROD) {
    define("CONF_BASE_URL", "#confluence-url_for_production#");
} else {
    define("CONF_BASE_URL", "#https://confluence-url_for_testing#");
}
