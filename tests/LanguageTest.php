<?php
/**
 * Created by IntelliJ IDEA.
 * User: mikks
 * Date: 3/6/2019
 * Time: 3:18 PM
 */

namespace Sk\Mid\Tests;
use PHPUnit\Framework\TestCase;
use Sk\Mid\Language1\ENG;
use Sk\Mid\Language1\EST;
use Sk\Mid\Language1\LIT;
use Sk\Mid\Language1\RUS;


class LanguageTest extends TestCase
{
    /**
     * @test
     */
    public function getEstLanguageInstanceAsType() {
        $estLanguage = new EST();
        self::assertEquals(new EST(), $estLanguage::asType());
    }

    /**
     * @test
     */
    public function getEngLanguageInstanceAsType() {
        $engLanguage = new ENG();
        self::assertEquals(new ENG(), $engLanguage::asType());
    }

    /**
     * @test
     */
    public function getRusLanguageInstanceAsType() {
        $rusLanguage = new RUS();
        self::assertEquals(new RUS(), $rusLanguage::asType());
    }

    /**
     * @test
     */
    public function getLitLanguageInstanceAsType() {
        $litLanguage = new LIT();
        self::assertEquals(new LIT(), $litLanguage::asType());
    }
}
