<?php

/**
 *
 */
class ReportHelper
{
    public function sanitizeFilters($filters) {
      return array(
          'country'       => empty($_POST['country']) ? null : $_POST['country'],
          'start'         => empty($_POST['start']) ? null : $_POST['start'],
          'end'           => empty($_POST['end']) ? null : $_POST['end'],
          'daysRemaining' => empty($_POST['daysRemaining']) ? null : $_POST['daysRemaining']
      );
    }

    public function formatReturnData($success, $step, $data = null) {
        switch ($step) {
            case 'activations':
                return json_encode([
                    'success'     => $success,
                    'activations' => $data,
                    'count'       => count($data)
                ]);
                break;
            case 'gigya':
                $_SESSION['csvData'] = $data;
                return json_encode([
                    'success' => $success,
                    'gigya'   => $data,
                    'count'   => count($data)
                ]);
                break;
            default:
                return json_encode([
                    'success' => $success,
                    'message' => 'No activations associated with the entitlement/s and filters'
                ]);
                break;
        }
    }
}
