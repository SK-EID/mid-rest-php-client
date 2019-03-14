<?php
/*-
 * #%L
 * Mobile ID sample PHP client
 * %%
 * Copyright (C) 2018 - 2019 SK ID Solutions AS
 * %%
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 * #L%
 */
namespace Sk\Mid\Util;

class Logger
{

    /** @var string $className */
    private $className;

    /** @var bool $traceEnabled */
    private $traceEnabled;

    /** @var bool $debugEnabled */
    private $debugEnabled;

    public function __construct(string $class)
    {
        $this->className = $class;
        $this->traceEnabled = false;
        $this->debugEnabled = false;
    }

    public function isTraceEnabled() : bool
    {
        return $this->traceEnabled;
    }

    public function setTraceEnabled(bool $traceEnabled) : void
    {
        $this->traceEnabled = $traceEnabled;
    }

    public function isDebugEnabled() : bool
    {
        return $this->debugEnabled;
    }

    public function setDebugEnabled(bool $debugEnabled) : void
    {
        $this->debugEnabled = $debugEnabled;
    }

    public function error(string $errorMessage) : void
    {
        $this->debug_to_console(date("H:i:s").' '.$this->className.' error: '.$errorMessage);
    }

    public function debug(string $debugMessage) : void
    {
        $this->debug_to_console(date("H:i:s").' '.$this->className.' debug: '.$debugMessage);
    }

    public function trace(string $traceMessage) : void
    {
        $this->debug_to_console(date("H:i:s").' '.$this->className.' trace: '.$traceMessage);
    }

    private function debug_to_console(string $message) : void {
        // you can add logging here
    }
}
