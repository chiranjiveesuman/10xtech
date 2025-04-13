<?php

namespace Drupal\style_guide\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Controller for the Style Guide page.
 */
class StyleGuideController extends ControllerBase {

  /**
   * Returns the sample person data.
   *
   * @return array
   *   An array of sample person data.
   */
  protected function getSampleData() {
    // Sample person data for the card.
    $person = [
      'name' => 'Jane Cooper',
      'position' => 'Paradigm Representative',
      'image' => 'https://i.pravatar.cc/300?img=5',
      'email' => 'jane.cooper@example.com',
      'phone' => '+1 (555) 123-4567',
      'admin' => TRUE,
    ];

    // Sample data for the grid.
    $sample_data = [
      [
        'name' => 'Leslie Alexander',
        'position' => 'Co-Founder / CEO',
        'image' => 'https://i.pravatar.cc/300?img=1',
        'email' => 'leslie.alexander@example.com',
        'phone' => '+1 (555) 765-4321',
        'admin' => TRUE,
      ],
      [
        'name' => 'Michael Foster',
        'position' => 'Frontend Developer',
        'image' => 'https://i.pravatar.cc/300?img=2',
        'email' => 'michael.foster@example.com',
        'phone' => '+1 (555) 234-5678',
        'admin' => FALSE,
      ],
      [
        'name' => 'Dries Buytaert',
        'position' => 'Drupal Founder',
        'image' => 'https://i.pravatar.cc/300?img=3',
        'email' => 'dries@example.com',
        'phone' => '+1 (555) 876-5432',
        'admin' => TRUE,
      ],
      [
        'name' => 'Whitney Francis',
        'position' => 'UX Designer',
        'image' => 'https://i.pravatar.cc/300?img=4',
        'email' => 'whitney.francis@example.com',
        'phone' => '+1 (555) 345-6789',
        'admin' => FALSE,
      ],
      [
        'name' => 'Jane Cooper',
        'position' => 'Product Manager',
        'image' => 'https://i.pravatar.cc/300?img=5',
        'email' => 'jane.cooper@example.com',
        'phone' => '+1 (555) 123-4567',
        'admin' => TRUE,
      ],
      [
        'name' => 'Cameron Williamson',
        'position' => 'Backend Developer',
        'image' => 'https://i.pravatar.cc/300?img=6',
        'email' => 'cameron.williamson@example.com',
        'phone' => '+1 (555) 456-7890',
        'admin' => FALSE,
      ],
      [
        'name' => 'Courtney Henry',
        'position' => 'Project Manager',
        'image' => 'https://i.pravatar.cc/300?img=7',
        'email' => 'courtney.henry@example.com',
        'phone' => '+1 (555) 567-8901',
        'admin' => TRUE,
      ],
      [
        'name' => 'Theresa Webb',
        'position' => 'DevOps Engineer',
        'image' => 'https://i.pravatar.cc/300?img=8',
        'email' => 'theresa.webb@example.com',
        'phone' => '+1 (555) 678-9012',
        'admin' => FALSE,
      ],
      [
        'name' => 'Cody Fisher',
        'position' => 'QA Specialist',
        'image' => 'https://i.pravatar.cc/300?img=9',
        'email' => 'cody.fisher@example.com',
        'phone' => '+1 (555) 789-0123',
        'admin' => FALSE,
      ],
      [
        'name' => 'Kristin Watson',
        'position' => 'Content Strategist',
        'image' => 'https://i.pravatar.cc/300?img=10',
        'email' => 'kristin.watson@example.com',
        'phone' => '+1 (555) 890-1234',
        'admin' => TRUE,
      ],
    ];

    return [
      'person' => $person,
      'persons' => $sample_data,
    ];
  }

  /**
   * Returns the content for the style guide page.
   *
   * @return array
   *   A render array.
   */
  public function content() {
    $data = $this->getSampleData();

    return [
      '#theme' => 'style_guide',
      '#person' => $data['person'],
      '#persons' => $data['persons'],
      '#attached' => [
        'library' => [
          'style_guide/tailwind',
        ],
      ],
    ];
  }

  /**
   * Returns the content for the centered style guide page.
   *
   * @return array
   *   A render array.
   */
  public function centeredContent() {
    $data = $this->getSampleData();

    return [
      '#theme' => 'style_guide_centered',
      '#person' => $data['person'],
      '#persons' => $data['persons'],
      '#attached' => [
        'library' => [
          'style_guide/tailwind',
        ],
      ],
    ];
  }

}
