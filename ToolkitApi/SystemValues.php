<?php

namespace ToolkitApi;

/**
 * Class SystemValues.
 *
 * Manages system values and interactions with the Toolkit service.
 */
class SystemValues
{
    /**
     * The toolkit service object used for interacting with the IBM i system.
     *
     * @var ToolkitInterface|null
     */
    private $ToolkitSrvObj;

    /**
     * Holds the error message when an error occurs during operations.
     *
     * @var string|null
     */
    private $ErrMessage = null;

    /**
     * Initializes the system values class with an optional Toolkit service object.
     * If no object is provided, the ToolkitSrvObj will be null.
     *
     * @param ToolkitInterface|null $ToolkitSrvObj The toolkit service object to interact with IBM i system. Default is null.
     *
     * @return $this|false returns the instance if Toolkit is provided, otherwise false
     */
    public function __construct(?ToolkitInterface $ToolkitSrvObj = null)
    {
        if ($ToolkitSrvObj instanceof Toolkit) {
            $this->ToolkitSrvObj = $ToolkitSrvObj;

            return $this;
        } else {
            return false;
        }
    }

    /**
     * Sets the connection to the toolkit service.
     *
     * @deprecated deprecate this method in the future
     *
     * @param string $dbname the database name to connect to
     * @param string $user   the username for the connection
     * @param string $pass   the password for the connection
     */
    public function setConnection($dbname, $user, $pass)
    {
        if (!$this->ToolkitSrvObj instanceof Toolkit) {
            $this->ToolkitSrvObj = new Toolkit($dbname, $user, $pass);
        }
    }

    /**
     * Retrieves a list of system values from the IBM i system.
     *
     * Executes the WRKSYSVAL OUTPUT(*PRINT) command to fetch system values. Returns an array of system values
     * containing 'Name', 'CurrentValue', 'ShippedValue', and 'Description'. Returns false if no values are found
     * or if an error occurs.
     *
     * @return array|bool an array of system values on success, or false on failure
     */
    public function systemValuesList()
    {
        if (!$this->ToolkitSrvObj instanceof Toolkit) {
            return false;
        }

        $tmparray = $this->ToolkitSrvObj->CLInteractiveCommand('WRKSYSVAL OUTPUT(*PRINT)');

        if (isset($tmparray)) {
            $i = 4;
            $sysvals = [];
            while (isset($tmparray[$i + 1])) {
                $tmparr = trim($tmparray[++$i]);
                if (substr($tmparr, 0, 1) == 'Q') {
                    $len = strlen($tmparr);
                    $sysvals[] = [
                        'Name'           => substr($tmparr, 0, 10),
                        'CurrentValue'   => substr($tmparr, 15, 32),
                        'ShippedValue'   => substr($tmparr, 47, 32),
                        'Description'    => substr($tmparr, 79, ($len - 79)),
                    ];
                }
            }

            return count($sysvals) > 0 ? $sysvals : false; // Return false if no system values founds
        } else {
            return false;
        }
    }

    /**
     * Retrieves the value of a specified system value from the IBM i system.
     *
     * Calls the RTVSYSVAL program to retrieve the system value by its name. Returns the system value if found,
     * or sets an error if the retrieval fails.
     *
     * @param string $sysValueName the name of the system value to retrieve
     *
     * @return string|false the system value on success, or false if an error occurs
     *
     * @todo Implement QWCRSVAL to support two-tier operations while maintaining good performance.
     */
    public function getSystemValue($sysValueName)
    {
        if (!$this->ToolkitSrvObj instanceof Toolkit) {
            return false;
        }

        $Err = ' ';
        $SysValue = ' ';
        $params[] = $this->ToolkitSrvObj->AddParameterChar('both', 1, 'ErrorCode', 'errorcode', $Err);
        $params[] = $this->ToolkitSrvObj->AddParameterChar('both', 10, 'SysValName', 'sysvalname', $sysValueName);
        $params[] = $this->ToolkitSrvObj->AddParameterChar('both', 1024, 'SysValue', 'sysval', $SysValue);
        $retArr = $this->ToolkitSrvObj->PgmCall(ZSTOOLKITPGM, $this->ToolkitSrvObj->getOption('HelperLib'), $params, null, ['func' => 'RTVSYSVAL']);

        if ($retArr !== false && isset($retArr['io_param'])) {
            $sysval = $retArr['io_param'];
            if (isset($sysval['sysvalname'])) {
                return $sysval['sysval'];
            } else {
                $this->setError($sysval['errorcode']);
            }
        }

        return false;
    }

    /**
     * Retrieves the current error message.
     *
     * @return string|null the error message
     */
    public function getError()
    {
        return $this->ErrMessage;
    }

    /**
     * Sets an error message based on the provided error code.
     *
     * @param string $errCode the error code that determines the error message
     */
    private function setError($errCode)
    {
        if ($errCode == '') /*clear error message*/ {
            $this->ErrMessage = '';

            return;
        }

        if ($errCode == '1') {
            $this->ErrMessage = 'System value data is not available.';
        } else {
            if ($errCode == '2') {
                $this->ErrMessage = 'System value can not be retrieved. ';
            }
        }
    }
}
