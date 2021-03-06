<?php
/**
 * neuralyzer : Data Anonymization Library and CLI Tool
 *
 * PHP Version 7.1
 *
 * @author Emmanuel Dyan
 * @author Rémi Sauvat
 * @copyright 2018 Emmanuel Dyan
 *
 * @package edyan/neuralyzer
 *
 * @license GNU General Public License v2.0
 *
 * @link https://github.com/edyan/neuralyzer
 */

namespace Edyan\Neuralyzer;

use Edyan\Neuralyzer\Exception\NeuralizerGuesserException;

/**
 * Guesser to map field type to Faker Class
 */
class Guesser implements GuesserInterface
{

    /**
     * Returns the version of your guesser
     *
     * @return string
     */
    public function getVersion(): string
    {
        return '3.0';
    }

    /**
     * Returns an array of fieldName => Faker class
     *
     * @return array
     */
    public function getColsNameMapping(): array
    {
        // can contain regexp
        return [
            // Internet
            '.*email.*'                  => ['method' => 'email'],
            '.*url'                      => ['method' => 'url'],

            // Adress and coordinates
            '.*address.*'                => ['method' => 'streetAddress'],
            '.*street.*'                 => ['method' => 'streetAddress'],
            '.*postalcode.*'             => ['method' => 'postcode'],
            '.*city.*'                   => ['method' => 'city'],
            '.*state.*'                  => ['method' => 'state'],
            '.*country.*'                => ['method' => 'country'],
            '.*phone.*'                  => ['method' => 'phoneNumber'],

            // Text
            '.*\.(comments|description)' => ['method' => 'sentence', 'params' => [20]],

            // Person
            '.*first_?name'              => ['method' => 'firstName'],
            '.*last_?name'               => ['method' => 'lastName'],
        ];
    }


    /**
     * Returns an array of fieldType => Faker method
     * @param  mixed $length  Field's length
     *
     * @return array
     */
    public function getColsTypeMapping($length): array
    {
        return [
            // Strings
            'string'     => ['method' => 'sentence', 'params' => [$length]],

            // Text & Blobs
            'text'       => ['method' => 'sentence', 'params' => [20]],
            'blob'       => ['method' => 'sentence', 'params' => [20]],

            // DateTime
            'date'       => ['method' => 'date',     'params' => ['Y-m-d']],
            'datetime'   => ['method' => 'date',     'params' => ['Y-m-d H:i:s']],
            'time'       => ['method' => 'time',     'params' => ['H:i:s']],

            // Integer
            'boolean'    => ['method' => 'boolean',      'params' => [4]],
            'smallint'   => ['method' => 'randomNumber', 'params' => [4]],
            'integer'    => ['method' => 'randomNumber', 'params' => [9]],
            'bigint'     => ['method' => 'randomNumber', 'params' => [strlen(mt_getrandmax()) - 1]],

            // Decimal
            'float'      => ['method' => 'randomFloat', 'params' => [2, 0, 999999]],
            'decimal'    => ['method' => 'randomFloat', 'params' => [2, 0, 999999]],
        ];
    }

    /**
     * Will map cols first by looking for field name then by looking for field type
     * if the first returned nothing
     *
     * @param string $table
     * @param string $name
     * @param string $type
     * @param mixed  $len    Used to get options from enum (stored in length)
     *
     * @return array
     */
    public function mapCol(string $table, string $name, string $type, string $len = null): array
    {
        // Try to find by colsName
        $colsName = $this->getColsNameMapping();
        foreach ($colsName as $colRegex => $params) {
            preg_match("/^$colRegex\$/i", $table. '.' . $name, $matches);
            if (!empty($matches)) {
                return $params;
            }
        }

        // Hardcoded types
        if ($type === 'enum') {
            return [
                'method' => 'randomElement',
                'params' => [explode("','", substr($len, 1, -1))]
            ];
        }

        // Try to find by fieldType
        $colsType = $this->getColsTypeMapping($len);
        if (!array_key_exists($type, $colsType)) {
            $msg = "Can't guess the type $type ({$table}.{$name})";
            throw new NeuralizerGuesserException($msg);
        }

        return $colsType[$type];
    }
}
