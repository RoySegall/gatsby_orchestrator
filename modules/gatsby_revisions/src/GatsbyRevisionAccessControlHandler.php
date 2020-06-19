<?php

namespace Drupal\gatsby_revisions;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines the access control handler for the gatsby revision entity type.
 */
class GatsbyRevisionAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {

    switch ($operation) {
      case 'view':
        return AccessResult::allowedIfHasPermission($account, 'view gatsby revision');

      case 'update':
        return AccessResult::allowedIfHasPermissions($account, ['edit gatsby revision', 'administer gatsby revision'], 'OR');

      case 'delete':
        return AccessResult::allowedIfHasPermissions($account, ['delete gatsby revision', 'administer gatsby revision'], 'OR');

      default:
        // No opinion.
        return AccessResult::neutral();
    }

  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermissions($account, ['create gatsby revision', 'administer gatsby revision'], 'OR');
  }

}
