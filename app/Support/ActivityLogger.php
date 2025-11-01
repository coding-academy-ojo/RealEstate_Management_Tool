<?php

namespace App\Support;

use App\Models\Activity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
  protected ?string $action = null;
  protected ?Model $subject = null;
  protected array $properties = [];
  protected ?string $description = null;

  /**
   * Set the action
   */
  public function performedAction(string $action): self
  {
    $this->action = $action;
    return $this;
  }

  /**
   * Shorthand methods for common actions
   */
  public function created(): self
  {
    return $this->performedAction('created');
  }

  public function updated(): self
  {
    return $this->performedAction('updated');
  }

  public function deleted(): self
  {
    return $this->performedAction('deleted');
  }

  public function restored(): self
  {
    return $this->performedAction('restored');
  }

  /**
   * Set the subject model
   */
  public function on(Model $subject): self
  {
    $this->subject = $subject;
    return $this;
  }

  /**
   * Set additional properties
   */
  public function withProperties(array $properties): self
  {
    $this->properties = $properties;
    return $this;
  }

  /**
   * Set custom description
   */
  public function withDescription(string $description): self
  {
    $this->description = $description;
    return $this;
  }

  /**
   * Log the activity
   */
  public function log(): Activity
  {
    if (!$this->action || !$this->subject) {
      throw new \Exception('Activity action and subject are required');
    }

    return log_activity(
      $this->action,
      $this->subject,
      $this->properties,
      $this->description
    );
  }
}
