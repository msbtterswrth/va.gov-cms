<?php

namespace Drupal\va_gov_consumers\Git;

/**
 * Access to the Github  API.
 */
interface GithubInterface {

  /**
   * Search Pull Requests.
   *
   * @param string $search_on
   *   Search string.
   * @param int $count
   *   Number of pull requests to return.
   *
   * @return array
   *   Array of pull request names.
   */
  public function searchPullRequests(string $search_on, int $count = 10) : array;

  /**
   * Manually trigger an action.
   *
   * @param string $action_name
   *   The name of the action to trigger.
   * @param string $ref
   *   The branch, tag, or commitish that the action should run against.
   * @param array $params
   *   A list of named params to pass to the action as arguments. Keys should
   *   match action input names.
   */
  public function triggerWorkflow(string $action_name, string $ref, array $params = []) : void;

  /**
   * List workflow runs for an action.
   *
   * @param string $action_name
   *   The name of the action to trigger.
   * @param array $params
   *   A list of named params to pass to the action as arguments. Keys should
   *   match the action input names.
   */
  public function listWorkflowRuns(string $action_name, array $params = []) : array;

  /**
   * Send a repository dispatch event.
   *
   * @param string $event_type
   *   The type of dispatch to send.
   * @param object $client_payload
   *   Optional extra data to send as the payload with the dispatch.
   */
  public function repositoryDispatch(string $event_type, object $client_payload = NULL) : void;

}
