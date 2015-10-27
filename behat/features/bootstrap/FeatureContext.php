<?php

use Drupal\DrupalExtension\Context\MinkContext;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Behat\Tester\Exception\PendingException;

class FeatureContext extends MinkContext implements SnippetAcceptingContext {

  /**
   * @Given I am an anonymous user
   */
  public function iAmAnAnonymousUser() {
    // Just let this pass-through.
  }

  /**
   * @When I visit the homepage
   */
  public function iVisitTheHomepage() {
    $this->getSession()->visit($this->locatePath('/'));
  }

  /**
   * @Then I should have access to the page
   */
  public function iShouldHaveAccessToThePage() {
    $this->assertSession()->statusCodeEquals('200');
  }

  /**
   * @Then I should not have access to the page
   */
  public function iShouldNotHaveAccessToThePage() {
    $this->assertSession()->statusCodeEquals('403');
  }

  /**
   * @When I go to log in page
   */
  public function iGoToLogInPage() {
    $this->iWaitForCssElement('.action-nav');
    $login = $this->getSession()->getPage()->find('named', array('link', 'Log In'));
    $this->getSession()->wait(4000);
    $login->click();
  }

  /**
   * @When I log in as registered user
   */
  public function iLogInAsRegisteredUser() {

    $this->iWaitForCssElement('#login-content');
    //Fill username and password
    $this->getSession()->getPage()->fillField('username', 'Shoov');
    $this->getSession()->getPage()->fillField('password', 'Gizra-123');
    $submit = $this->getSession()->getPage()->find('named', array('button','Log in'));
    $this->getSession()->wait(4000);
    $submit->submit();
  }

  /**
   * @Then I should see the dashboard as registered user
   */
  public function iShouldSeeTheDashboardAsRegisteredUser()
  {
    $this->getSession()->wait(4000);
//    $url = $this->getSession()->getCurrentUrl();
//    $url = $url . 'account/details/';

    $this->visit($this->getSession()->getCurrentUrl() . 'account/details/');
    $this->getSession()->wait(4000);
    $this->iWaitForCssElement('#details_form');
    $name = $this->getSession()->getPage()->find('css', '#account-name');
    if ($name->getValue() != 'Gizra') {
      throw new \Exception('Account name is wrong');
    }
  }

  /**
   * @Then /^I wait for css element "([^"]*)" to "([^"]*)"$/
   */
  public function iWaitForCssElement($element, $appear = 'appear') {
    $xpath = $this->getSession()->getSelectorsHandler()->selectorToXpath('css', $element);
    $this->waitForXpathNode($xpath, $appear == 'appear');
  }

  /**
   * Helper function; Execute a function until it return TRUE or timeouts.
   *
   * @param $fn
   *   A callable to invoke.
   * @param int $timeout
   *   The timeout period. Defaults to 10 seconds.
   *
   * @throws Exception
   */
  private function waitFor($fn, $timeout = 15000) {
    $start = microtime(true);
    $end = $start + $timeout / 1000.0;
    while (microtime(true) < $end) {
      if ($fn($this)) {
        return;
      }
    }
    throw new \Exception('waitFor timed out.');
  }

  /**
   * Wait for an element by its XPath to appear or disappear.
   *
   * @param string $xpath
   *   The XPath string.
   * @param bool $appear
   *   Determine if element should appear. Defaults to TRUE.
   *
   * @throws Exception
   */
  private function waitForXpathNode($xpath, $appear = TRUE) {
    $this->waitFor(function($context) use ($xpath, $appear) {
      try {
        $nodes = $context->getSession()->getDriver()->find($xpath);
        if (count($nodes) > 0) {
          $visible = $nodes[0]->isVisible();
          return $appear ? $visible : !$visible;
        }
        return !$appear;
      }
      catch (WebDriver\Exception $e) {
        if ($e->getCode() == WebDriver\Exception::NO_SUCH_ELEMENT) {
          return !$appear;
        }
        throw $e;
      }
    });
  }
}
