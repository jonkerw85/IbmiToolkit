<?php

namespace ToolkitApi;

/**
 * Class Toolkit.
 */
interface ToolkitInterface
{
    /**
     * @param $isCw
     */
    public function setIsCw($isCw);

    /**
     * @return bool
     */
    public function getIsCw();

    /**
     * @return array
     */
    public function validPlugSizes();

    public function setToolkitServiceParams(array $XmlServiceOptions);

    /**
     * @param $optionName
     *
     * @return bool|void
     *
     * @throws \Exception
     */
    public function getOption($optionName);

    /**
     * @return array
     */
    public function getOptions();

    /**
     * @param array $options
     */
    public function setOptions($options = []);

    /**
     * @param $optionName
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function getToolkitServiceParam($optionName);

    public function disconnect();

    public function disconnectPersistent();

    /**
     * @param $stringToLog
     */
    public function debugLog($stringToLog);

    /**
     * @return bool
     */
    public function isDb2();

    /**
     * @return bool
     */
    public function setDb2();

    /**
     * @param $callType
     *
     * @return array|bool
     */
    public function specialCall($callType);

    /**
     * @return array|bool
     */
    public function callTransportOnly();

    /**
     * @return array|bool
     */
    public function performanceData();

    /**
     * @return array|bool
     */
    public function licenseXMLSERVICE();

    /**
     * @param string $pgmName     Name of program to call, without library
     * @param string $lib         Library of program. Leave blank to use library list or current library
     * @param null   $inputParam  an array of ProgramParameter objects OR XML representing params, to be sent as-is
     * @param null   $returnParam ReturnValue Array of one parameter that's the return value parameter
     * @param null   $options     Array of other options. The most popular is 'func' indicating the name of a subprocedure or function.
     *
     * @return array|bool
     */
    public function pgmCall($pgmName, $lib, $inputParam = null, $returnParam = null, $options = null);

    /**
     * @return string
     */
    public function getErrorMsg();

    /**
     * @return string
     */
    public function getErrorCode();

    /**
     * @param $msg
     */
    public function setErrorMsg($msg);

    /**
     * @param $code
     */
    public function setErrorCode($code);

    /**
     * @return bool
     */
    public function getOutputParam(array $OutputArray);

    /**
     * @param $inputXml
     * @param bool $disconnect
     *
     * @return string
     *
     * @throws \Exception
     */
    public function ExecuteProgram($inputXml, $disconnect = false);

    /**
     * @param $inputXml
     * @param bool $disconnect
     *
     * @return string return output XML
     */
    public function sendXml($inputXml, $disconnect = false);

    /**
     * @param string $info      can be 'joblog' (joblog and additional info) or 'conf' (if custom config info set up in PLUGCONF)
     * @param string $jobName
     * @param string $jobUser
     * @param string $jobNumber
     *
     * @return bool|void
     */
    public function getDiagnostics($info = 'joblog', $jobName = '', $jobUser = '', $jobNumber = '');

    /**
     * @return string Version number (e.g. '1.4.0')
     */
    public static function getFrontEndVersion();

    /**
     * @return string Version
     */
    public function getBackEndVersion();

    /**
     * @param $library
     *
     * @return string Version number (e.g. '1.8.0')
     */
    public static function getLocalBackEndVersion($library);

    /**
     * @param array  $command string will be turned into an array
     * @param string $exec    could be 'pase', 'pasecmd', 'system,' 'rexx', or 'cmd'
     *
     * @return array|bool
     */
    public function CLCommand($command, $exec = '');

    /**
     * @param $command
     *
     * @return array|bool
     */
    public function CLInteractiveCommand($command);

    /**
     * @param $command
     *
     * @return array|bool
     */
    public function paseCommand($command);

    /**
     * @param $command
     *
     * @return bool
     */
    public function qshellCommand($command);

    /**
     * @param $command
     *
     * @return array|bool
     */
    public function ClCommandWithOutput($command);

    /**
     * @param string $command can be a string or an array
     *
     * @return array|bool
     */
    public function ClCommandWithCpf($command);

    /**
     * @param $type
     * @param $io
     * @param $comment
     * @param string $varName
     * @param string $value
     * @param string $varying
     * @param int    $dimension
     *
     * @return array
     */
    public static function AddParameter($type, $io, $comment, $varName = '', $value = '', $varying = 'off', $dimension = 0);

    /**
     * @param $io
     * @param $size
     * @param $comment
     * @param string $varName
     * @param string $value
     * @param string $varying
     * @param int    $dimension
     * @param string $by
     * @param bool   $isArray
     * @param string $ccsidBefore
     * @param string $ccsidAfter
     * @param bool   $useHex
     *
     * @return CharParam
     */
    public static function AddParameterChar($io, $size, $comment, $varName = '', $value = '', $varying = 'off', $dimension = 0, $by = '', $isArray = false, $ccsidBefore = '', $ccsidAfter = '', $useHex = false);

    /**
     * @param $io
     * @param $comment
     * @param string $varName
     * @param string $value
     * @param int    $dimension
     *
     * @return Int32Param
     */
    public static function AddParameterInt32($io, $comment, $varName = '', $value = '', $dimension = 0);

    /**
     * @param $comment
     * @param string $varName
     * @param $labelFindLen
     *
     * @return SizeParam
     */
    public static function AddParameterSize($comment, $varName = '', $labelFindLen = 0);

    /**
     * @param $comment
     * @param string $varName
     * @param $labelFindLen
     *
     * @return SizePackParam
     */
    public static function AddParameterSizePack($comment, $varName = '', $labelFindLen = 0);

    /**
     * @param $io
     * @param $comment
     * @param string $varName
     * @param string $value
     * @param int    $dimension
     *
     * @return Int8Param
     */
    public static function AddParameterInt8($io, $comment, $varName = '', $value = '', $dimension = 0);

    /**
     * @param $io
     * @param $comment
     * @param string $varName
     * @param string $value
     * @param int    $dimension
     *
     * @return Int16Param
     */
    public static function AddParameterInt16($io, $comment, $varName = '', $value = '', $dimension = 0);

    /**
     * @param $io
     * @param $comment
     * @param string $varName
     * @param string $value
     * @param int    $dimension
     *
     * @return Int64Param
     */
    public static function AddParameterInt64($io, $comment, $varName = '', $value = '', $dimension = 0);

    /**
     * @param $io
     * @param $comment
     * @param string $varName
     * @param string $value
     * @param int    $dimension
     *
     * @return UInt8Param
     */
    public static function AddParameterUInt8($io, $comment, $varName = '', $value = '', $dimension = 0);

    /**
     * @param $io
     * @param $comment
     * @param string $varName
     * @param string $value
     * @param int    $dimension
     *
     * @return UInt16Param
     */
    public static function AddParameterUInt16($io, $comment, $varName = '', $value = '', $dimension = 0);

    /**
     * @param $io
     * @param $comment
     * @param string $varName
     * @param string $value
     * @param int    $dimension
     *
     * @return UInt32Param
     */
    public static function AddParameterUInt32($io, $comment, $varName = '', $value = '', $dimension = 0);

    /**
     * @param $io
     * @param $comment
     * @param string $varName
     * @param string $value
     * @param int    $dimension
     *
     * @return UInt64Param
     */
    public static function AddParameterUInt64($io, $comment, $varName = '', $value = '', $dimension = 0);

    /**
     * @param $io
     * @param $comment
     * @param string $varName
     * @param string $value
     * @param int    $dimension
     *
     * @return FloatParam
     */
    public static function AddParameterFloat($io, $comment, $varName = '', $value = '', $dimension = 0);

    /**
     * @param $io
     * @param $comment
     * @param string $varName
     * @param string $value
     * @param int    $dimension
     *
     * @return RealParam
     */
    public static function AddParameterReal($io, $comment, $varName = '', $value = '', $dimension = 0);

    /**
     * @param $io
     * @param $length
     * @param $scale
     * @param $comment
     * @param string $varName
     * @param string $value
     * @param int    $dimension
     *
     * @return PackedDecParam
     */
    public static function AddParameterPackDec($io, $length, $scale, $comment, $varName = '', $value = '', $dimension = 0);

    /**
     * @param $io
     * @param $length
     * @param $scale
     * @param $comment
     * @param string $varName
     * @param string $value
     * @param int    $dimension
     *
     * @return ZonedParam
     */
    public static function AddParameterZoned($io, $length, $scale, $comment, $varName = '', $value = '', $dimension = 0);

    /**
     * @param $size
     * @param string $comment
     *
     * @return HoleParam
     */
    public static function AddParameterHole($size, $comment = 'hole');

    /**
     * @param $io
     * @param $size
     * @param $comment
     * @param string $varName
     * @param string $value
     * @param int    $dimension
     *
     * @return BinParam
     */
    public static function AddParameterBin($io, $size, $comment, $varName = '', $value = '', $dimension = 0);

    /**
     * @param $array
     *
     * @return array
     */
    public static function AddParameterArray($array);

    /**
     * @param string $name
     * @param int    $dim
     * @param string $by
     * @param bool   $isArray
     * @param null   $labelLen
     * @param string $comment
     * @param string $io
     *
     * @return DataStructure
     */
    public static function AddDataStruct(array $parameters, $name = 'struct_name', $dim = 0, $by = '', $isArray = false, $labelLen = null, $comment = '', $io = 'both');

    /**
     * @return DataStructure
     */
    public static function AddErrorDataStruct();

    /**
     * @return DataStructure
     */
    public static function AddErrorDataStructZeroBytes();

    /**
     * @param int $paramNum
     *
     * @return string
     */
    public static function getErrorDataStructXml($paramNum = 0);

    /**
     * @param int $paramNum
     *
     * @return string
     */
    public static function getErrorDataStructXmlWithCode($paramNum = 0);

    /**
     * @param int $paramNum
     *
     * @return string
     */
    public static function getListInfoApiXml($paramNum = 0);

    /**
     * @param int $paramNum
     *
     * @return string
     */
    public static function getNumberOfRecordsDesiredApiXml($paramNum = 0);

    /**
     * @param int $paramNum
     *
     * @return string
     */
    public static function getSortInformationApiXml($paramNum = 0);

    /**
     * @param int $paramNum
     * @param $lengthOfReceiverVariable
     *
     * @return string
     */
    public static function getDummyReceiverAndLengthApiXml($paramNum, $lengthOfReceiverVariable);

    /**
     * @return string
     */
    public function getLastError();

    /**
     * @return bool
     */
    public function isError();

    /**
     * @return bool|void
     */
    public function getInternalKey();

    /**
     * @return bool|void
     */
    public function isStateless();

    /**
     * @param $internalKey
     */
    public function setInternalKey($internalKey);

    /**
     * @return mixed
     */
    public function getXmlOut();

    /**
     * @return resource|null
     */
    public function getConnection();

    /**
     * @return string
     */
    public function generate_name();

    /**
     * @return array
     */
    public static function GenerateErrorParameter();

    /**
     * @return array
     */
    public static function GenerateErrorParameterZeroBytes();

    /**
     * @param $retPgmArr
     * @param $functionErrMsg
     *
     * @return bool
     */
    public function verify_CPFError($retPgmArr, $functionErrMsg);

    /**
     * @return bool
     */
    public function ParseErrorParameter(array $Error);

    /**
     * @return resource|null
     */
    public function getSQLConnection();

    /**
     * @param $stmt
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function executeQuery($stmt);

    /**
     * @param bool $isPersistent
     *
     * @throws \Exception
     */
    public function setIsPersistent($isPersistent = false);

    /**
     * @return bool
     */
    public function getIsPersistent();

    /**
     * @return array|bool array of attributes (key/value pairs) or false if unsuccessful
     */
    public function getJobAttributes();

    /**
     * @return string
     */
    public static function classPath();

    /**
     * @param string $user     Generally should be uppercase
     * @param string $password
     *
     * @return bool True on success, False on failure
     */
    public function changeCurrentUser($user, $password);

    /**
     * @param $heading
     * @param $key
     * @param null $default
     *
     * @return bool|null
     */
    public static function getConfigValue($heading, $key, $default = null);

    /**
     * @return string
     */
    public static function getPhpOperatingSystem();

    /**
     * @return bool
     */
    public static function isPhpRunningOnIbmI();

    /**
     * @return bool
     */
    public static function getPhpCcsid();
}
