<?php

namespace Drupal\administerusersbyrole\Tests;

use Drupal\simpletest\WebTestBase;
use Drupal\Component\Utility\String;

/**
 * Testing for administerusersbyrole module
 *
 * @group administerusersbyrole
 */
class AdministerusersbyroleTestCaseTest extends WebTestBase {

  public static $modules = array('administerusersbyrole', 'user', 'node');

  protected $roles = array();
  protected $users = array();

  public static function getInfo() {
    return array(
      'name' => 'administerusersbyrole',
      'description' => 'Ensure that Administer Users by Role functions properly.',
      'group' => 'Administer Users by Role',
    );
  }

  public function setUp() {
    parent::setUp();

    $this->createUserWithRole('noroles', array());
    $this->createRolesAndUsers('alpha', FALSE);
    $this->createRolesAndUsers('beta', TRUE);
    $this->createUserWithRole('alphabeta', array('alpha', 'beta'));

    // alphabeta_ed
    $perms = array(
      'access content',
      _administerusersbyrole_build_perm_string($this->roles['alpha'], 'edit'),
      _administerusersbyrole_build_perm_string($this->roles['beta'], 'edit'),
    );
    $this->resetAll();
    $this->roles['alphabeta_ed'] = $this->drupalCreateRole($perms, 'alphabeta_ed');
    $this->createUserWithRole('alphabeta_ed', array('alphabeta_ed'));

    // all_editor
    $perms = array(
      'access content',
      'edit users with no custom roles',
    );
    foreach ($this->roles as $roleName => $roleID) {
      $perms[] = _administerusersbyrole_build_perm_string($this->roles[$roleName], 'edit');
    }
    $this->resetAll();
    $this->roles['all_editor'] = $this->drupalCreateRole($perms, 'all_editor');
    $this->createUserWithRole('all_editor', array('all_editor'));

    // all_deletor
    $perms = array(
      'access content',
      'cancel users with no custom roles',
    );
    foreach ($this->roles as $roleName => $roleID) {
      $perms[] = _administerusersbyrole_build_perm_string($roleID, 'cancel');
    }
    $this->resetAll();
    $this->roles['all_deletor'] = $this->drupalCreateRole($perms, 'all_deletor');
    $this->createUserWithRole('all_deletor', array('all_deletor'));

    // creator
    $perms = array(
      'access content',
      'create users',
    );
    $this->resetAll();
    $this->roles['creator'] = $this->drupalCreateRole($perms, 'creator');
    $this->createUserWithRole('creator', array('creator'));
  }

  public function testPermissions() {
    $expectations = array(
      // When I'm logged in as...
      'nobody' => array(
        // ...I can perform these actions on this other user...
        'noroles'      => array('edit' => FALSE, 'cancel' => FALSE),
        'alpha'        => array('edit' => FALSE, 'cancel' => FALSE),
        'alpha_editor' => array('edit' => FALSE, 'cancel' => FALSE),
        'beta'         => array('edit' => FALSE, 'cancel' => FALSE),
        'beta_editor'  => array('edit' => FALSE, 'cancel' => FALSE),
        'alphabeta'    => array('edit' => FALSE, 'cancel' => FALSE),
        'alphabeta_ed' => array('edit' => FALSE, 'cancel' => FALSE),
        'creator'      => array('edit' => FALSE, 'cancel' => FALSE),
        'all_editor'   => array('edit' => FALSE, 'cancel' => FALSE),
        'all_deletor'  => array('edit' => FALSE, 'cancel' => FALSE),
        'create users' => FALSE,
      ),
      'noroles' => array(
        'noroles'      => array('edit' => TRUE,  'cancel' => TRUE),
        'alpha'        => array('edit' => FALSE, 'cancel' => FALSE),
        'alpha_editor' => array('edit' => FALSE, 'cancel' => FALSE),
        'beta'         => array('edit' => FALSE, 'cancel' => FALSE),
        'beta_editor'  => array('edit' => FALSE, 'cancel' => FALSE),
        'alphabeta'    => array('edit' => FALSE, 'cancel' => FALSE),
        'alphabeta_ed' => array('edit' => FALSE, 'cancel' => FALSE),
        'creator'      => array('edit' => FALSE, 'cancel' => FALSE),
        'all_editor'   => array('edit' => FALSE, 'cancel' => FALSE),
        'all_deletor'  => array('edit' => FALSE, 'cancel' => FALSE),
        'create users' => FALSE,
      ),
      'alpha' => array(
        'noroles'      => array('edit' => FALSE, 'cancel' => FALSE),
        'alpha'        => array('edit' => TRUE,  'cancel' => TRUE),
        'alpha_editor' => array('edit' => FALSE, 'cancel' => FALSE),
        'beta'         => array('edit' => FALSE, 'cancel' => FALSE),
        'beta_editor'  => array('edit' => FALSE, 'cancel' => FALSE),
        'alphabeta'    => array('edit' => FALSE, 'cancel' => FALSE),
        'alphabeta_ed' => array('edit' => FALSE, 'cancel' => FALSE),
        'creator'      => array('edit' => FALSE, 'cancel' => FALSE),
        'all_editor'   => array('edit' => FALSE, 'cancel' => FALSE),
        'all_deletor'  => array('edit' => FALSE, 'cancel' => FALSE),
        'create users' => FALSE,
      ),
      'alpha_editor' => array(
        'noroles'      => array('edit' => TRUE,  'cancel' => FALSE),
        'alpha'        => array('edit' => TRUE,  'cancel' => FALSE),
        'alpha_editor' => array('edit' => TRUE,  'cancel' => TRUE),
        'beta'         => array('edit' => FALSE, 'cancel' => FALSE),
        'beta_editor'  => array('edit' => FALSE, 'cancel' => FALSE),
        'alphabeta'    => array('edit' => TRUE, 'cancel' => FALSE),
        'alphabeta_ed' => array('edit' => FALSE, 'cancel' => FALSE),
        'creator'      => array('edit' => FALSE, 'cancel' => FALSE),
        'all_editor'   => array('edit' => FALSE, 'cancel' => FALSE),
        'all_deletor'  => array('edit' => FALSE, 'cancel' => FALSE),
        'create users' => FALSE,
      ),
      'beta' => array(
        'noroles'      => array('edit' => FALSE, 'cancel' => FALSE),
        'alpha'        => array('edit' => FALSE, 'cancel' => FALSE),
        'alpha_editor' => array('edit' => FALSE, 'cancel' => FALSE),
        'beta'         => array('edit' => TRUE,  'cancel' => TRUE),
        'beta_editor'  => array('edit' => FALSE, 'cancel' => FALSE),
        'alphabeta'    => array('edit' => FALSE, 'cancel' => FALSE),
        'alphabeta_ed' => array('edit' => FALSE, 'cancel' => FALSE),
        'creator'      => array('edit' => FALSE, 'cancel' => FALSE),
        'all_editor'   => array('edit' => FALSE, 'cancel' => FALSE),
        'all_deletor'  => array('edit' => FALSE, 'cancel' => FALSE),
        'create users' => FALSE,
      ),
      'beta_editor' => array(
        'noroles'      => array('edit' => TRUE,  'cancel' => FALSE),
        'alpha'        => array('edit' => FALSE, 'cancel' => FALSE),
        'alpha_editor' => array('edit' => FALSE, 'cancel' => FALSE),
        'beta'         => array('edit' => TRUE,  'cancel' => TRUE),
        'beta_editor'  => array('edit' => TRUE,  'cancel' => TRUE),
        'alphabeta'    => array('edit' => TRUE, 'cancel' => TRUE),
        'alphabeta_ed' => array('edit' => FALSE, 'cancel' => FALSE),
        'creator'      => array('edit' => FALSE, 'cancel' => FALSE),
        'all_editor'   => array('edit' => FALSE, 'cancel' => FALSE),
        'all_deletor'  => array('edit' => FALSE, 'cancel' => FALSE),
        'create users' => FALSE,
      ),
      'alphabeta' => array(
        'noroles'      => array('edit' => FALSE, 'cancel' => FALSE),
        'alpha'        => array('edit' => FALSE, 'cancel' => FALSE),
        'alpha_editor' => array('edit' => FALSE, 'cancel' => FALSE),
        'beta'         => array('edit' => FALSE, 'cancel' => FALSE),
        'beta_editor'  => array('edit' => FALSE, 'cancel' => FALSE),
        'alphabeta'    => array('edit' => TRUE,  'cancel' => TRUE),
        'alphabeta_ed' => array('edit' => FALSE, 'cancel' => FALSE),
        'creator'      => array('edit' => FALSE, 'cancel' => FALSE),
        'all_editor'   => array('edit' => FALSE, 'cancel' => FALSE),
        'all_deletor'  => array('edit' => FALSE, 'cancel' => FALSE),
        'create users' => FALSE,
      ),
      'alphabeta_ed' => array(
        'noroles'      => array('edit' => FALSE, 'cancel' => FALSE),
        'alpha'        => array('edit' => TRUE,  'cancel' => FALSE),
        'alpha_editor' => array('edit' => FALSE, 'cancel' => FALSE),
        'beta'         => array('edit' => TRUE,  'cancel' => FALSE),
        'beta_editor'  => array('edit' => FALSE, 'cancel' => FALSE),
        'alphabeta'    => array('edit' => TRUE,  'cancel' => FALSE),
        'alphabeta_ed' => array('edit' => TRUE,  'cancel' => TRUE),
        'creator'      => array('edit' => FALSE, 'cancel' => FALSE),
        'all_editor'   => array('edit' => FALSE, 'cancel' => FALSE),
        'all_deletor'  => array('edit' => FALSE, 'cancel' => FALSE),
        'create users' => FALSE,
      ),
      'all_editor' => array(
        'noroles'      => array('edit' => TRUE,  'cancel' => FALSE),
        'alpha'        => array('edit' => TRUE,  'cancel' => FALSE),
        'alpha_editor' => array('edit' => TRUE,  'cancel' => FALSE),
        'beta'         => array('edit' => TRUE,  'cancel' => FALSE),
        'beta_editor'  => array('edit' => TRUE,  'cancel' => FALSE),
        'alphabeta'    => array('edit' => TRUE,  'cancel' => FALSE),
        'alphabeta_ed' => array('edit' => TRUE,  'cancel' => FALSE),
        'creator'      => array('edit' => FALSE, 'cancel' => FALSE),
        'all_editor'   => array('edit' => TRUE,  'cancel' => TRUE),
        'all_deletor'  => array('edit' => FALSE, 'cancel' => FALSE),
        'create users' => FALSE,
      ),
      'all_deletor' => array(
        'noroles'      => array('edit' => FALSE, 'cancel' => TRUE),
        'alpha'        => array('edit' => FALSE, 'cancel' => TRUE),
        'alpha_editor' => array('edit' => FALSE, 'cancel' => TRUE),
        'beta'         => array('edit' => FALSE, 'cancel' => TRUE),
        'beta_editor'  => array('edit' => FALSE, 'cancel' => TRUE),
        'alphabeta'    => array('edit' => FALSE, 'cancel' => TRUE),
        'alphabeta_ed' => array('edit' => FALSE, 'cancel' => TRUE),
        'creator'      => array('edit' => FALSE, 'cancel' => FALSE),
        'all_editor'   => array('edit' => FALSE, 'cancel' => TRUE),
        'all_deletor'  => array('edit' => TRUE,  'cancel' => TRUE),
        'create users' => FALSE,
      ),
      'creator' => array(
        'noroles'      => array('edit' => FALSE, 'cancel' => FALSE),
        'alpha'        => array('edit' => FALSE, 'cancel' => FALSE),
        'alpha_editor' => array('edit' => FALSE, 'cancel' => FALSE),
        'beta'         => array('edit' => FALSE, 'cancel' => FALSE),
        'beta_editor'  => array('edit' => FALSE, 'cancel' => FALSE),
        'alphabeta'    => array('edit' => FALSE, 'cancel' => FALSE),
        'alphabeta_ed' => array('edit' => FALSE, 'cancel' => FALSE),
        'creator'      => array('edit' => TRUE,  'cancel' => TRUE),
        'all_editor'   => array('edit' => FALSE, 'cancel' => FALSE),
        'all_deletor'  => array('edit' => FALSE, 'cancel' => FALSE),
        'create users' => TRUE,
      ),
    );

    foreach ($expectations as $loginUsername => $editUsernames) {
      if ($loginUsername !== 'nobody') {
        $this->drupalLogin($this->users[$loginUsername]);
      }

      foreach ($editUsernames as $k => $v) {
        if ($k === 'create users') {
          $this->drupalGet("admin/people/create");
          $expectedResult = $v;
          if ($expectedResult) {
            $this->assertRaw('All emails from the system will be sent to this address', "My expectation is that $loginUsername should be able to create users, but it can't.");
          }
          else {
            $this->assertRaw('You are not authorized to access this page.', "My expectation is that $loginUsername shouldn't be able to create users, but it can.");
          }
        }
        else {
          $editUsername = $k;
          $operations = $v;
          $editUid = $this->users[$editUsername]->id();
          foreach ($operations as $operation => $expectedResult) {
            $this->drupalGet("user/$editUid/$operation");
            if ($expectedResult) {
              if ($operation === 'edit') {
                $this->assertRaw("All emails from the system will be sent to this address.", "My expectation is that $loginUsername should be able to $operation $editUsername, but it can't.");
              }
              elseif ($operation === 'cancel') {
                $this->assertRaw("Are you sure you want to cancel", "My expectation is that $loginUsername should be able to $operation $editUsername, but it can't.");
              }
            }
            else {
              $this->assertTrue(
                strstr($this->getRawContent(), "You do not have permission to $operation <em class=\"placeholder\">$editUsername</em>.")
                || strstr($this->getRawContent(), 'Access denied'),
                "My expectation is that $loginUsername shouldn't be able to $operation $editUsername, but it can.");
            }
          }
        }
      }

      if ($loginUsername !== 'nobody') {
        $this->drupalLogout();
      }
    }
  }

  protected function createUserWithRole($userName, $roleNames) {

    $this->users[$userName] = $this->createUser($roleNames, $userName);

    //$id = $account->id();
    //$this->drupalGet("user/$id/edit");

    $this->assertTrue($this->users[$userName]->id() > 0, "Unable to create user $userName.");
  }

  protected function createRolesAndUsers($roleName, $allowEditorToCancel) {
    // create basic role
    $this->roles[$roleName] = $this->drupalCreateRole(array('access content'), $roleName);
    $this->createUserWithRole($roleName, array($roleName));

    // clear permissions cache, so we can use the 'edit users with ...' permission for this role
    $this->resetAll();
    // create role to edit above role and also anyone with no custom roles.
    $perms = array(
      'access content',
      'edit users with no custom roles',
      _administerusersbyrole_build_perm_string($this->roles[$roleName], 'edit'),
    );
    if ($allowEditorToCancel) {
      // Don't add in "no custom roles" this time, to give better variety of testing.
      $perms[] = _administerusersbyrole_build_perm_string($this->roles[$roleName], 'cancel');
    }
    $this->roles["{$roleName}_editor"] = $this->drupalCreateRole($perms, "{$roleName}_editor");
    $this->createUserWithRole("{$roleName}_editor", array("{$roleName}_editor"));
  }

  /**
  * Allows create users with determined roles
  */
  protected function createUser(array $roles = array(), $name = NULL) {
     // Create a user assigned to that role.
    $edit = array();
    $edit['name'] = !empty($name) ? $name : $this->randomMachineName();
    $edit['mail'] = $edit['name'] . '@example.com';
    $edit['pass'] = 'cheese';
    $edit['status'] = 1;
    if (sizeof($roles) > 0) {
      $edit['roles'] = $roles;
    }

    $account = entity_create('user', $edit);
    $account->save();

    $this->assertTrue($account->id(), String::format('User created with name %name and pass %pass', array('%name' => $edit['name'], '%pass' => $edit['pass'])), 'User login');
    if (!$account->id()) {
      return FALSE;
    }

    // Add the raw password so that we can log in as this user.
    $account->pass_raw = $edit['pass'];
    return $account;
  }
}
