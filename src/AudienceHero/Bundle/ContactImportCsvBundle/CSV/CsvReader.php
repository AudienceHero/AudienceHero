<?php

/*
 * This file is part of the AudienceHero project.
 *
 * (c) Marc Weistroff <marc@weistroff.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace AudienceHero\Bundle\ContactImportCsvBundle\CSV;

use League\Csv\Reader;
use League\Csv\Statement;
use voku\helper\UTF8;

/**
 * CsvReader.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class CsvReader
{
    /** @var Reader */
    private $reader;

    /**
     * Returns a new CsvReader instance initialized with $content.
     * $content is clean from UTF-8 weird characters.
     *
     * @param string $content
     */
    public function __construct(string $content)
    {
        if (!$content) {
            $content = '';
        }

        $this->reader = Reader::createFromString(UTF8::clean($content, true, true, true));
    }

    /**
     * Returns the header of the CSV.
     *
     * @return array
     */
    public function getHeader(): array
    {
        $this->reader->setHeaderOffset(0);

        return $this->reader->getHeader();
    }

    /**
     * Extract a data sample of $length rows from the CSV.
     *
     * @param int $length
     *
     * @return array
     */
    public function extractSample(int $length): array
    {
        $stmt = new Statement();
        $stmt = $stmt->offset(1)->limit($length);

        $records = [];

        foreach ($stmt->process($this->reader)->getRecords() as $record) {
            $records[] = $record;
        }

        return $records;
    }

    public function getRecords(array $columns): iterable
    {
        $this->reader->setHeaderOffset(0);
        $records = (new Statement())->process($this->reader, $columns);

        return $records;
    }
}
