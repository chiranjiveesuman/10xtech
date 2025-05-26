<?php

namespace Drupal\document_approval\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\user\UserInterface;

/**
 * Defines the Document Submission entity.
 *
 * @ContentEntityType(
 *   id = "document_submission",
 *   label = @Translation("Document Submission"),
 *   base_table = "document_submission",
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid",
 *     "user_id" = "user_id",
 *     "status" = "status"
 *   },
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\Core\Entity\EntityListBuilder",
 *     "form" = {
 *       "default" = "Drupal\Core\Entity\ContentEntityForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm"
 *     }
 *   }
 * )
 */
class DocumentSubmission extends ContentEntityBase {

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('User'))
      ->setDescription(t('The user who submitted the documents.'))
      ->setSetting('target_type', 'user')
      ->setRequired(TRUE);

    $fields['status'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Status'))
      ->setDescription(t('The status of the submission.'))
      ->setDefaultValue('pending')
      ->setRequired(TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time the submission was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time the submission was last edited.'));

    $fields['doc1'] = BaseFieldDefinition::create('file')
      ->setLabel(t('Document 1'))
      ->setDescription(t('Upload document 1'))
      ->setRequired(TRUE)
      ->setSettings([
        'file_directory' => 'document_submissions/[date:custom:Y]-[date:custom:m]',
        'file_extensions' => 'pdf jpg jpeg',
        'max_filesize' => '10 MB',
      ])
      ->setDisplayOptions('form', [
        'type' => 'file',
        'weight' => 0,
      ]);

    $fields['doc2'] = BaseFieldDefinition::create('file')
      ->setLabel(t('Document 2'))
      ->setDescription(t('Upload document 2'))
      ->setRequired(TRUE)
      ->setSettings([
        'file_directory' => 'document_submissions/[date:custom:Y]-[date:custom:m]',
        'file_extensions' => 'pdf jpg jpeg',
        'max_filesize' => '10 MB',
      ])
      ->setDisplayOptions('form', [
        'type' => 'file',
        'weight' => 1,
      ]);

    $fields['doc3'] = BaseFieldDefinition::create('file')
      ->setLabel(t('Document 3'))
      ->setDescription(t('Upload document 3'))
      ->setRequired(TRUE)
      ->setSettings([
        'file_directory' => 'document_submissions/[date:custom:Y]-[date:custom:m]',
        'file_extensions' => 'pdf jpg jpeg',
        'max_filesize' => '10 MB',
      ])
      ->setDisplayOptions('form', [
        'type' => 'file',
        'weight' => 2,
      ]);

    $fields['doc4'] = BaseFieldDefinition::create('file')
      ->setLabel(t('Document 4 (Optional)'))
      ->setDescription(t('Upload document 4 (optional)'))
      ->setRequired(FALSE)
      ->setSettings([
        'file_directory' => 'document_submissions/[date:custom:Y]-[date:custom:m]',
        'file_extensions' => 'pdf jpg jpeg',
        'max_filesize' => '10 MB',
      ])
      ->setDisplayOptions('form', [
        'type' => 'file',
        'weight' => 3,
      ]);

    // Add status fields for each document
    for ($i = 1; $i <= 4; $i++) {
      $fields["doc{$i}_status"] = BaseFieldDefinition::create('string')
        ->setLabel(t('Document @num Status', ['@num' => $i]))
        ->setDescription(t('The approval status for document @num', ['@num' => $i]))
        ->setDefaultValue('pending');

      $fields["doc{$i}_comments"] = BaseFieldDefinition::create('string_long')
        ->setLabel(t('Document @num Comments', ['@num' => $i]))
        ->setDescription(t('Admin comments for document @num', ['@num' => $i]));
    }

    return $fields;
  }

  /**
   * Gets the submission status.
   */
  public function getStatus() {
    return $this->get('status')->value;
  }

  /**
   * Sets the submission status.
   */
  public function setStatus($status) {
    $this->set('status', $status);
    return $this;
  }

  /**
   * Gets the user ID.
   */
  public function getUser() {
    return $this->get('user_id')->entity;
  }

  /**
   * Sets the user ID.
   */
  public function setUser(UserInterface $user) {
    $this->set('user_id', $user->id());
    return $this;
  }
}
