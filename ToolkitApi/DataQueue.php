<?php

namespace ToolkitApi;

/**
 * Class DataQueue.
 *
 * Manages interaction with IBM i Data Queue (DTAQ) services via the Toolkit interface.
 */
class DataQueue
{
    private $Toolkit;
    private $DataQueueName;
    private $DataQueueLib;
    private $CPFErr = '0000000';
    private $ErrMessage = null;

    /**
     * Initializes the DataQueue object with an optional Toolkit service object.
     * If no object is provided, the Toolkit property will remain uninitialized.
     *
     * @param ToolkitInterface|null $ToolkitSrvObj an instance of the Toolkit service, or null
     */
    public function __construct(?ToolkitInterface $ToolkitSrvObj = null)
    {
        if ($ToolkitSrvObj instanceof Toolkit) {
            $this->Toolkit = $ToolkitSrvObj;

            return $this;
        }

        return false;
    }

    /**
     * Retrieves the error message, if any.
     *
     * This method returns the current error message stored in the ErrMessage property. If no error has occurred, it returns null.
     *
     * @return string|null the error message, or null if no error has occurred
     */
    public function getError()
    {
        return $this->ErrMessage;
    }

    /**
     * Sets the error message.
     *
     * This method sets the ErrMessage property to the provided error string. This is typically used to store the error message when an operation fails.
     *
     * @param string $error the error message to set
     *
     * @return void
     */
    public function setError(string $error)
    {
        $this->ErrMessage = $error;
    }

    /**
     * Create a new data queue in the specified library.
     *
     * @param string $DataQName           name of the data queue to be created
     * @param string $DataQLib            library where the data queue will be created
     * @param int    $MaxLength           Maximum length of data in the queue. Default is 128.
     * @param string $Sequence            Sequence type for the queue (*FIFO, *LIFO, *KEYED). Default is *FIFO.
     * @param int    $KeyLength           Key length for *KEYED queues. Default is 0.
     * @param string $Authority           Authority for the queue. Default is *LIBCRTAUT.
     * @param int    $QSizeMaxNumEntries  Maximum number of entries in the queue. Default is 32999.
     * @param int    $QSizeInitNumEntries Initial number of entries in the queue. Default is 16.
     *
     * @return bool returns true if the data queue is successfully created
     *
     * @throws \Exception throws an exception if the data queue creation fails
     */
    public function CreateDataQ($DataQName, $DataQLib,
                                $MaxLength = 128,
                                $Sequence = '*FIFO', $KeyLength = 0,
                                $Authority = '*LIBCRTAUT',
                                $QSizeMaxNumEntries = 32999, $QSizeInitNumEntries = 16)
    {
        $this->DataQueueName = $DataQName;
        $this->DataQueueLib = $DataQLib;

        if (strcmp(strtoupper($Sequence), '*KEYED') == 0 ||
            strcmp(strtoupper($Sequence), '*FIFO') == 0 ||
            strcmp(strtoupper($Sequence), '*LIFO') == 0) {
            $DataQType = $Sequence;
        } else {
            $this->setError('Invalid Data Queue type parameter');

            return false;
        }

        $KeyedSetting = '';

        if (strcmp(strtoupper($Sequence), '*KEYED') == 0) {
            $DQKeylen = min($KeyLength, 256);
            $KeyedSetting = "KEYLEN($DQKeylen)";
        }

        // @todo validation: if $KeyLength supplied, sequence must be *KEYED.

        if (is_integer($QSizeMaxNumEntries)) {
            $MaxQSize = $QSizeMaxNumEntries;
        } else {
            if (strcmp($QSizeMaxNumEntries, '*MAX16MB') == 0 || strcmp($QSizeMaxNumEntries, '*MAX2GB') == 0) {
                $MaxQSize = (string) $QSizeMaxNumEntries;
            }
        }

        if ($QSizeInitNumEntries > 0) {
            $InitEntryies = $QSizeInitNumEntries;
        }

        if (!isset($MaxQSize) || !isset($InitEntryies)) {
            throw new \Exception('Could not generate additional settings because MaxQSize and/or InitEntryies are not set or are invalid.');
        }

        $AdditionalSetting = sprintf("$KeyedSetting SENDERID(*YES) SIZE(%s %d)", $MaxQSize, $InitEntryies);

        ($MaxLength > 64512) ? $MaxLen = 64512 : $MaxLen = $MaxLength;

        $cmd = sprintf('QSYS/CRTDTAQ DTAQ(%s/%s) MAXLEN(%s) SEQ(%s) %s  AUT(%s)',
            $this->DataQueueLib,$this->DataQueueName,
            $MaxLen, $DataQType, $AdditionalSetting, $Authority);

        if (!$this->Toolkit->CLCommand($cmd)) {
            $this->ErrMessage = 'Create Data Queue failed.' . $this->Toolkit->getLastError();
            throw new \Exception($this->ErrMessage);
        }

        return true;
    }

    /**
     * Deletes a specified Data Queue (DTAQ) from the IBM i system.
     *
     * Uses the DLTDTAQ command to delete the Data Queue. If no queue name or library is provided,
     * it will use the object's current DataQueueName and DataQueueLib.
     *
     * @param string $DataQName The name of the Data Queue to delete. Defaults to the object's DataQueueName.
     * @param string $DataQLib  The library where the Data Queue resides. Defaults to the object's DataQueueLib.
     *
     * @return bool true on success
     *
     * @throws \Exception if the command fails, an exception with the error message will be thrown
     */
    public function DeleteDQ($DataQName = '', $DataQLib = '')
    {
        $cmd = sprintf('QSYS/DLTDTAQ DTAQ(%s/%s)',
            ($DataQLib != '' ? $DataQLib : $this->DataQueueLib),
            ($DataQName != null ? $DataQName : $this->DataQueueName));

        if (!$this->Toolkit->CLCommand($cmd)) {
            $this->ErrMessage = 'Delete Data Queue failed.' . $this->Toolkit->getLastError();
            throw new \Exception($this->ErrMessage);
        }

        return true;
    }

    /**
     * Correctly calls the misspelled receieveDataQueue method.
     *
     * This method provides an alias for receieveDataQueue, ensuring backwards compatibility.
     *
     * @param int    $WaitTime      The amount of time (in seconds) to wait for data. A negative value waits indefinitely.
     * @param string $KeyOrder      the order of the key data, defaults to empty
     * @param int    $KeyLength     the length of the key data, defaults to 0
     * @param string $KeyData       the key data to search for, defaults to an empty string
     * @param string $WithRemoveMsg Flag to indicate whether the message should be removed after retrieval. Defaults to 'N' (No).
     *
     * @return bool returns the result of the call to receieveDataQueue
     */
    public function receiveDataQueue($WaitTime, $KeyOrder = '', $KeyLength = 0, $KeyData = '', $WithRemoveMsg = 'N')
    {
        // call misspelled one
        return $this->receieveDataQueue($WaitTime, $KeyOrder, $KeyLength, $KeyData, $WithRemoveMsg);
    }

    /**
     * Receives data from the specified Data Queue (DTAQ) using the QRCVDTAQ API.
     *
     * This method interacts with the IBM i system's QRCVDTAQ API to receive data from a Data Queue.
     * It allows for waiting a specified time and provides options for key-based filtering and removing messages.
     *
     * @param int    $WaitTime      The amount of time (in seconds) to wait for data. A negative value waits indefinitely.
     * @param string $KeyOrder      The order of the key data (e.g., EQ), defaults to empty.
     * @param int    $KeyLength     the length of the key data, defaults to 0
     * @param string $KeyData       the key data to search for, defaults to an empty string
     * @param string $WithRemoveMsg Flag to indicate whether the message should be removed after retrieval. Defaults to 'N' (No).
     *
     * @return bool|array returns false if no data is found, or an array containing the data from the queue on success
     *
     * @throws \Exception if an error occurs while receiving the data
     */
    public function receieveDataQueue($WaitTime, $KeyOrder = '', $KeyLength = 0, $KeyData = '', $WithRemoveMsg = 'N')
    {
        // uses QRCVDTAQ API
        // http://publib.boulder.ibm.com/infocenter/iseries/v5r3/index.jsp?topic=%2Fapis%2Fqrcvdtaq.htm

        $params[] = $this->Toolkit->AddParameterChar('in', 10, 'dqname', 'dqname', $this->DataQueueName);
        $params[] = $this->Toolkit->AddParameterChar('in', 10, 'dqlib', 'dqlib', $this->DataQueueLib);

        // @todo do not hard-code data size. Use system of labels as allowed by XMLSERVICE (as done in CW's i5_dtaq_receive).
        $DataLen = 300;
        $Data = ' ';

        $params[] = $this->Toolkit->AddParameterPackDec('out', 5, 0, 'datalen', 'datalen', $DataLen); // @todo this is output only so no need to specify a value
        $params[] = $this->Toolkit->AddParameterChar('out', (int) $DataLen, 'datavalue', 'datavalue', $Data); // @todo this is output only so no need to specify a value.

        // Wait time: < 0 waits forever. 0 process immed. > 0 is number of seconds to wait.
        $params[] = $this->Toolkit->AddParameterPackDec('in', 5, 0, 'waittime', 'waittime', $WaitTime);

        if (!$KeyLength) {
            // 0, make order, length and data also zero or blank, so thatthey'll be ignored by API. Must send them, though.

            // if an unkeyed queue, API still expects to receive key info,
            // but it must be blank and zero.
            $KeyOrder = ''; // e.g. EQ, other operators, or blank
            $KeyLength = 0;
            $KeyData = '';
        }

        $params[] = $this->Toolkit->AddParameterChar('in', 2, 'keydataorder', 'keydataorder', $KeyOrder);
        $params[] = $this->Toolkit->AddParameterPackDec('in', 3, 0, 'keydatalen', 'keydatalen', $KeyLength);
        $params[] = $this->Toolkit->AddParameterChar('both', (int) $KeyLength, 'keydata', 'keydata', $KeyData);

        $params[] = $this->Toolkit->AddParameterPackDec('in', 3, 0, 'senderinflen', 'senderinflen', 44);
        // Sender info may contain packed data, so don't receive it till we can put it in a data structure.
        // @todo use a data structure to receive sender info as defined in QRCVDTAQ spec.
        $params[] = $this->Toolkit->AddParameterHole(44, 'senderinf');

        // whether to remove message from data queue
        if ($WithRemoveMsg == 'N') {
            $Remove = '*NO       ';
        } else {
            $Remove = '*YES      ';
        }

        $params[] = $this->Toolkit->AddParameterChar('in', 10, 'remove', 'remove', $Remove);
        // @todo note from API manual: If this parameter is not specified, the entire message will be copied into the receiver variable.
        $params[] = $this->Toolkit->AddParameterPackDec('in', 5, 0, 'size of data receiver', 'receiverSize', $DataLen);

        $params[] = $this->Toolkit->AddErrorDataStructZeroBytes(); // so errors bubble up to joblog

        $retPgmArr = $this->Toolkit->PgmCall('QRCVDTAQ', 'QSYS', $params);
        if (isset($retPgmArr['io_param'])) {
            $DQData = $retPgmArr['io_param'];

            if ($DQData['datalen'] > 0) {
                return $DQData;
            }
        }

        return false;
    }

    /**
     * Sets the Data Queue name and library.
     *
     * This method allows you to set the name and library for the Data Queue. These values are used in subsequent methods that interact with the Data Queue.
     *
     * @param string $DataQName the name of the Data Queue
     * @param string $DataQLib  the library containing the Data Queue
     *
     * @return void
     */
    public function SetDataQName($DataQName, $DataQLib)
    {
        $this->DataQueueName = $DataQName;
        $this->DataQueueLib = $DataQLib;
    }

    /**
     * Sends data to the specified Data Queue using the QSNDDTAQ API.
     *
     * This method sends data to the Data Queue, with optional key-based filtering. If a key length is provided, it will include key data in the request.
     *
     * @param int    $DataLen   the length of the data to send
     * @param string $Data      the data to send to the Data Queue
     * @param int    $KeyLength the length of the key data (optional, defaults to 0)
     * @param string $KeyData   the key data used for key-based filtering (optional, defaults to empty string)
     *
     * @return array|bool returns the response array on success or false on failure
     *
     * @throws \Exception if an error occurs during the API call
     */
    public function SendDataQueue($DataLen, $Data, $KeyLength = 0, $KeyData = '')
    {
        // QSNDDTAQ API:
        // http://publib.boulder.ibm.com/infocenter/iseries/v5r4/index.jsp?topic=%2Fapis%2Fqsnddtaq.htm

        $params[] = $this->Toolkit->AddParameterChar('in', 10, 'dqname', 'dqname', $this->DataQueueName);
        $params[] = $this->Toolkit->AddParameterChar('in', 10, 'dqlib', 'dqlib', $this->DataQueueLib);

        $params[] = $this->Toolkit->AddParameterPackDec('in', 5, 0, 'datalen', 'datalen', $DataLen, null);
        $params[] = $this->Toolkit->AddParameterChar('in', $DataLen, 'datavalue', 'datavalue', $Data);
        if ($KeyLength > 0) {
            $params[] = $this->Toolkit->AddParameterPackDec('in', 3, 0, 'keydatalen', 'keydatalen', $KeyLength, null);
            $params[] = $this->Toolkit->AddParameterChar('in', $KeyLength, 'keydata', 'keydata', $KeyData);
        }

        $ret = $this->Toolkit->PgmCall('QSNDDTAQ', 'QSYS', $params);

        return $ret;
    }

    /**
     * Clears the Data Queue using the QCLRDTAQ API, optionally specifying key-based filtering.
     *
     * This method clears all messages from the Data Queue. If key data is provided, it clears messages matching the specified key.
     *
     * @param string $KeyOrder  the order of the key data (optional, defaults to empty string)
     * @param int    $KeyLength the length of the key data (optional, defaults to 0)
     * @param string $KeyData   the key data to match for filtering (optional, defaults to empty string)
     *
     * @return bool returns true on success or false if the operation fails
     *
     * @throws \Exception if an error occurs during the API call
     */
    public function ClearDQ($KeyOrder = '', $KeyLength = 0, $KeyData = '')
    {
        //QCLRDTAQ
        $params[] = $this->Toolkit->AddParameterChar('in', 10, 'dqname', 'dqname', $this->DataQueueName);
        $params[] = $this->Toolkit->AddParameterChar('in', 10, 'dqlib', 'dqlib', $this->DataQueueLib);
        if ($KeyLength > 0) {
            $params[] = $this->Toolkit->AddParameterChar('in', 2, 'keydataorder', 'keydataorder', $KeyOrder);
            $params[] = $this->Toolkit->AddParameterPackDec('in', 3, 0, 'keydatalen', 'keydatalen', $KeyLength);
            $params[] = $this->Toolkit->AddParameterChar('in', ((int) $KeyLength), 'keydata', 'keydata', $KeyData);
            //$params[] = array('ds'=>$this->Toolkit->GenerateErrorParameter());
            $ds = $this->Toolkit->GenerateErrorParameter();
            $params[] = Toolkit::AddDataStruct($ds);
        }

        $retArr = $this->Toolkit->PgmCall('QCLRDTAQ', 'QSYS', $params);

        if (isset($retArr['exceptId']) && strcmp($retArr['exceptId'], '0000000')) {
            $this->CPFErr = $retArr['exceptId'];
            $this->ErrMessage = "Clear Data Queue failed. Error: $this->CPFErr";

            return false;
        }

        return true;
    }
}
