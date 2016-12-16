<?php

function sneeit_demo_installer_ajax_error($message = '', $delete_folder = null) {
	if (!$delete_folder) {
		$delete_folder = sneeit_get_server_request('folder');
		if ($delete_folder) {
			$delete_folder = SNEEIT_DEMO_INSTALLER_FOLDER.'/'.$delete_folder;
		}
	}
	if ($delete_folder) {
		sneeit_delete_file($delete_folder);
	}
	echo json_encode(array('error' => $message));
	die();
}
 // myPHPAdmin 'PMA_sqlAddSlashes()' function
function sneeit_sql_add_slashes($a_string = '', $is_like = false, $crlf = false, $php_code = false)
{
    if ($is_like) {
        $a_string = str_replace('\\', '\\\\\\\\', $a_string);
    } else {
        $a_string = str_replace('\\', '\\\\', $a_string);
    }

    if ($crlf) {
        $a_string = strtr(
            $a_string,
            array("\n" => '\n', "\r" => '\r', "\t" => '\t')
        );
    }

    if ($php_code) {
        $a_string = str_replace('\'', '\\\'', $a_string);
    } else {
        $a_string = str_replace('\'', '\'\'', $a_string);
    }

    return $a_string;
}
global $Sneeit_SQP_Data_Forbidden_Words;
$Sneeit_SQP_Data_Forbidden_Words = array (
    'ACCESSIBLE',       // 5.1
    'ACTION',
    'ADD',
    'AFTER',
    'AGAINST',
    'AGGREGATE',
    'ALGORITHM',
    'ALL',
    'ALTER',
    'ANALYZE',
    'AND',
    'ANY',
    'AS',
    'ASC',
    'ASCII',
    'ASENSITIVE',
    'AUTO_INCREMENT',
    'AVG',
    'AVG_ROW_LENGTH',
    'BACKUP',
    'BDB',
    'BEFORE',
    'BEGIN',
    'BERKELEYDB',
    'BETWEEN',
    'BIGINT',
    'BINARY',
    'BINLOG',
    'BIT',
    'BLOB',
    'BOOL',
    'BOOLEAN',
    'BOTH',
    'BTREE',
    'BY',
    'BYTE',
    'CACHE',
    'CALL',
    'CASCADE',
    'CASCADED',
    'CASE',
    'CHAIN',
    'CHANGE',
    'CHANGED',
    'CHAR',
    'CHARACTER',
    'CHARSET',
    'CHECK',
    'CHECKSUM',
    'CIPHER',
    'CLOSE',
    'COLLATE',
    'COLLATION',
    'COLUMN',
    'COLUMNS',
    'COMMENT',
    'COMMIT',
    'COMMITTED',
    'COMPACT',
    'COMPRESSED',
    'CONCURRENT',
    'CONDITION',
    'CONNECTION',
    'CONSISTENT',
    'CONSTRAINT',
    'CONTAINS',
    'CONTINUE',
    'CONVERT',
    'CREATE',
    'CROSS',
    'CUBE',
    'CURRENT_DATE',
    'CURRENT_TIME',
    'CURRENT_TIMESTAMP',
    'CURRENT_USER',
    'CURSOR',
    'DATA',
    'DATABASE',
    'DATABASES',
    'DATE',
    'DATETIME',
    'DAY',
    'DAY_HOUR',
    'DAY_MICROSECOND',
    'DAY_MINUTE',
    'DAY_SECOND',
    'DEALLOCATE',
    'DEC',
    'DECIMAL',
    'DECLARE',
    'DEFAULT',
    'DEFINER',
    'DELAYED',
    'DELAY_KEY_WRITE',
    'DELETE',
    'DESC',
    'DESCRIBE',
    'DES_KEY_FILE',
    'DETERMINISTIC',
    'DIRECTORY',
    'DISABLE',
    'DISCARD',
    'DISTINCT',
    'DISTINCTROW',
    'DIV',
    'DO',
    'DOUBLE',
    'DROP',
    'DUAL',
    'DUMPFILE',
    'DUPLICATE',
    'DYNAMIC',
    'EACH',
    'ELSE',
    'ELSEIF',
    'ENABLE',
    'ENCLOSED',
    'END',
    'ENGINE',
    'ENGINES',
    'ENUM',
    'ERRORS',
    'ESCAPE',
    'ESCAPED',
    'EVENTS',
    'EXECUTE',
    'EXISTS',
    'EXIT',
    'EXPANSION',
    'EXPLAIN',
    'EXTENDED',
    'FALSE',
    'FAST',
    'FETCH',
    'FIELDS',
    'FILE',
    'FIRST',
    'FIXED',
    'FLOAT',
    'FLOAT4',
    'FLOAT8',
    'FLUSH',
    'FOR',
    'FORCE',
    'FOREIGN',
    'FOUND',
    'FRAC_SECOND',
    'FROM',
    'FULL',
    'FULLTEXT',
    'FUNCTION',
    'GEOMETRY',
    'GEOMETRYCOLLECTION',
    'GET_FORMAT',
    'GLOBAL',
    'GOTO',
    'GRANT',
    'GRANTS',
    'GROUP',
    'HANDLER',
    'HASH',
    'HAVING',
    'HELP',
    'HIGH_PRIORITY',
    'HOSTS',
    'HOUR',
    'HOUR_MICROSECOND',
    'HOUR_MINUTE',
    'HOUR_SECOND',
    'IDENTIFIED',
    'IF',
    'IGNORE',
    'IMPORT',
    'IN',
    'INDEX',
    'INDEXES',
    'INFILE',
    'INNER',
    'INNOBASE',
    'INNODB',
    'INOUT',
    'INSENSITIVE',
    'INSERT',
    'INSERT_METHOD',
    'INT',
    'INT1',
    'INT2',
    'INT3',
    'INT4',
    'INT8',
    'INTEGER',
    'INTERVAL',
    'INTO',
    'INVOKER',
    'IO_THREAD',
    'IS',
    'ISOLATION',
    'ISSUER',
    'ITERATE',
    'JOIN',
    'KEY',
    'KEYS',
    'KILL',
    'LABEL',
    'LANGUAGE',
    'LAST',
    'LEADING',
    'LEAVE',
    'LEAVES',
    'LEFT',
    'LIKE',
    'LIMIT',
    'LINEAR',               // 5.1
    'LINES',
    'LINESTRING',
    'LOAD',
    'LOCAL',
    'LOCALTIME',
    'LOCALTIMESTAMP',
    'LOCK',
    'LOCKS',
    'LOGS',
    'LONG',
    'LONGBLOB',
    'LONGTEXT',
    'LOOP',
    'LOW_PRIORITY',
    'MASTER',
    'MASTER_CONNECT_RETRY',
    'MASTER_HOST',
    'MASTER_LOG_FILE',
    'MASTER_LOG_POS',
    'MASTER_PASSWORD',
    'MASTER_PORT',
    'MASTER_SERVER_ID',
    'MASTER_SSL',
    'MASTER_SSL_CA',
    'MASTER_SSL_CAPATH',
    'MASTER_SSL_CERT',
    'MASTER_SSL_CIPHER',
    'MASTER_SSL_KEY',
    'MASTER_USER',
    'MATCH',
    'MAX_CONNECTIONS_PER_HOUR',
    'MAX_QUERIES_PER_HOUR',
    'MAX_ROWS',
    'MAX_UPDATES_PER_HOUR',
    'MAX_USER_CONNECTIONS',
    'MEDIUM',
    'MEDIUMBLOB',
    'MEDIUMINT',
    'MEDIUMTEXT',
    'MERGE',
    'MICROSECOND',
    'MIDDLEINT',
    'MIGRATE',
    'MINUTE',
    'MINUTE_MICROSECOND',
    'MINUTE_SECOND',
    'MIN_ROWS',
    'MOD',
    'MODE',
    'MODIFIES',
    'MODIFY',
    'MONTH',
    'MULTILINESTRING',
    'MULTIPOINT',
    'MULTIPOLYGON',
    'MUTEX',
    'NAME',
    'NAMES',
    'NATIONAL',
    'NATURAL',
    'NCHAR',
    'NDB',
    'NDBCLUSTER',
    'NEW',
    'NEXT',
    'NO',
    'NONE',
    'NOT',
    'NO_WRITE_TO_BINLOG',
    'NULL',
    'NUMERIC',
    'NVARCHAR',
    'OFFSET',
    'OLD_PASSWORD',
    'ON',
    'ONE',
    'ONE_SHOT',
    'OPEN',
    'OPTIMIZE',
    'OPTION',
    'OPTIONALLY',
    'OR',
    'ORDER',
    'OUT',
    'OUTER',
    'OUTFILE',
    'PACK_KEYS',
    'PARTIAL',
    'PASSWORD',
    'PHASE',
    'POINT',
    'POLYGON',
    'PRECISION',
    'PREPARE',
    'PREV',
    'PRIMARY',
    'PRIVILEGES',
    'PROCEDURE',
    'PROCESSLIST',
    'PURGE',
    'QUARTER',
    'QUERY',
    'QUICK',
    'RAID0',
    'RAID_CHUNKS',
    'RAID_CHUNKSIZE',
    'RAID_TYPE',
    'RANGE',                // 5.1
    'READ',
    'READS',
    'READ_ONLY',            // 5.1
    'READ_WRITE',           // 5.1
    'REAL',
    'RECOVER',
    'REDUNDANT',
    'REFERENCES',
    'REGEXP',
    'RELAY_LOG_FILE',
    'RELAY_LOG_POS',
    'RELAY_THREAD',
    'RELEASE',
    'RELOAD',
    'RENAME',
    'REPAIR',
    'REPEAT',
    'REPEATABLE',
    'REPLACE',
    'REPLICATION',
    'REQUIRE',
    'RESET',
    'RESTORE',
    'RESTRICT',
    'RESUME',
    'RETURN',
    'RETURNS',
    'REVOKE',
    'RIGHT',
    'RLIKE',
    'ROLLBACK',
    'ROLLUP',
    'ROUTINE',
    'ROW',
    'ROWS',
    'ROW_FORMAT',
    'RTREE',
    'SAVEPOINT',
    'SCHEMA',
    'SCHEMAS',
    'SECOND',
    'SECOND_MICROSECOND',
    'SECURITY',
    'SELECT',
    'SENSITIVE',
    'SEPARATOR',
    'SERIAL',
    'SERIALIZABLE',
    'SESSION',
    'SET',
    'SHARE',
    'SHOW',
    'SHUTDOWN',
    'SIGNED',
    'SIMPLE',
    'SLAVE',
    'SMALLINT',
    'SNAPSHOT',
    'SOME',
    'SONAME',
    'SOUNDS',
    'SPATIAL',
    'SPECIFIC',
    'SQL',
    'SQLEXCEPTION',
    'SQLSTATE',
    'SQLWARNING',
    'SQL_BIG_RESULT',
    'SQL_BUFFER_RESULT',
    'SQL_CACHE',
    'SQL_CALC_FOUND_ROWS',
    'SQL_NO_CACHE',
    'SQL_SMALL_RESULT',
    'SQL_THREAD',
    'SQL_TSI_DAY',
    'SQL_TSI_FRAC_SECOND',
    'SQL_TSI_HOUR',
    'SQL_TSI_MINUTE',
    'SQL_TSI_MONTH',
    'SQL_TSI_QUARTER',
    'SQL_TSI_SECOND',
    'SQL_TSI_WEEK',
    'SQL_TSI_YEAR',
    'SSL',
    'START',
    'STARTING',
    'STATUS',
    'STOP',
    'STORAGE',
    'STRAIGHT_JOIN',
    'STRING',
    'STRIPED',
    'SUBJECT',
    'SUPER',
    'SUSPEND',
    'TABLE',
    'TABLES',
    'TABLESPACE',
    'TEMPORARY',
    'TEMPTABLE',
    'TERMINATED',
    'TEXT',
    'THEN',
    'TIME',
    'TIMESTAMP',
    'TIMESTAMPADD',
    'TIMESTAMPDIFF',
    'TINYBLOB',
    'TINYINT',
    'TINYTEXT',
    'TO',
    'TRAILING',
    'TRANSACTION',
    'TRIGGER',
    'TRIGGERS',
    'TRUE',
    'TRUNCATE',
    'TYPE',
    'TYPES',
    'UNCOMMITTED',
    'UNDEFINED',
    'UNDO',
    'UNICODE',
    'UNION',
    'UNIQUE',
    'UNKNOWN',
    'UNLOCK',
    'UNSIGNED',
    'UNTIL',
    'UPDATE',
    'USAGE',
    'USE',
    'USER',
    'USER_RESOURCES',
    'USE_FRM',
    'USING',
    'UTC_DATE',
    'UTC_TIME',
    'UTC_TIMESTAMP',
    'VALUE',
    'VALUES',
    'VARBINARY',
    'VARCHAR',
    'VARCHARACTER',
    'VARIABLES',
    'VARYING',
    'VIEW',
    'WARNINGS',
    'WEEK',
    'WHEN',
    'WHERE',
    'WHILE',
    'WITH',
    'WORK',
    'WRITE',
    'X509',
    'XA',
    'XOR',
    'YEAR',
    'YEAR_MONTH',
    'ZEROFILL'
);
function sneeit_backquote($a_name, $do_it = true) {
    if (is_array($a_name)) {
        foreach ($a_name as &$data) {
            $data = sneeit_backquote($data, $do_it);
        }
        return $a_name;
    }

    if (! $do_it) {
        global $Sneeit_SQP_Data_Forbidden_Words;

        if (! in_array(strtoupper($a_name), $Sneeit_SQP_Data_Forbidden_Words)) {
            return $a_name;
        }
    }

    // '0' is also empty for php :-(
    if (strlen($a_name) && $a_name !== '*') {
        return '`' . str_replace('`', '``', $a_name) . '`';
    } else {
        return $a_name;
    }
}