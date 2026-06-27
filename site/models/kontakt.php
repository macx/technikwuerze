<?php

declare(strict_types=1);

use Kirby\Cms\Page;

class KontaktPage extends Page
{
  public function actions(): array
  {
    return [TwContactFormAction::class];
  }
}
