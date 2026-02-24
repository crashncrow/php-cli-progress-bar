<?php
class Progress {
    // Bar size
    private $size = 10;

    // Processed tasks
    private $processed;

    // Total tasks
    private $tasks;

    // Starting time
    private $start;

    // Ending time
    private $end;

    // Progress bar name
    private $name;

    // Track whether a progress line has been rendered
    private $rendered = false;

    public function __construct( $name, $tasks, $processed = 0 ) {
        $this->start = microtime(true);
        $this->name = $name;
        $this->tasks = max(0, (int)$tasks);
        $this->processed = min(max(0, (int)$processed), $this->tasks);
        $this->render();
    }

    public function increase( $increment = 1 ) {
        if ($this->processed < $this->tasks) {
            $this->processed = min($this->processed + (int)$increment, $this->tasks);
            $this->render();
        }
        else {
            $this->newline();
        }
    }

    public function elapsed() {
        $now = microtime(true);
        $duration = $now - $this->start;
        $hh = str_pad((int)($duration / 60 / 60), 2, '0', STR_PAD_LEFT);
        $mm = str_pad((int)($duration / 60) - $hh * 60, 2, '0', STR_PAD_LEFT);
        $ss = str_pad((int)($duration - $hh * 60 * 60 - $mm * 60), 2, '0', STR_PAD_LEFT);

        return "$hh:$mm:$ss";
    }

    public function end( $show = true, $beer = true ) {
        $this->end = microtime(true);
        $this->newline();
        if ($show) {
            if ($beer) {
                fwrite(STDOUT, "\xF0\x9F\x8D\xBA ");
            }

            fwrite(STDOUT, "END {$this->name}" . PHP_EOL);
        }
        fwrite(STDOUT, PHP_EOL);
    }

    private function map( $value, $fromLow, $fromHigh, $toLow, $toHigh ) {
        $fromRange = $fromHigh - $fromLow;
        if ($fromRange == 0) {
            return $toHigh;
        }
        $toRange = $toHigh - $toLow;
        $scaleFactor = $toRange / $fromRange;

        // Re-zero the value within the from range
        $tmpValue = $value - $fromLow;
        // Rescale the value to the to range
        $tmpValue *= $scaleFactor;
        // Re-zero back to the to range
        return $tmpValue + $toLow;
    }

    private function newline() {
        if ($this->rendered) {
            fwrite(STDOUT, PHP_EOL);
            $this->rendered = false;
        }
    }

    private function render() {
        $step = (int)$this->map($this->processed, 0, max($this->tasks, 1), 0, $this->size);
        if ($this->tasks === 0) {
            $progress = 100;
        } else {
            $progress = min((int)($this->processed * 100 / $this->tasks), 100);
        }
        $progress = str_pad($progress, 3, ' ', STR_PAD_LEFT);

        // Write progress bar
        $line = "[\033[32m" . str_repeat('#', $step) . str_repeat('·', $this->size - $step) . "\033[0m]";
        $line .= " {$progress}% Complete - {$this->name} - {$this->processed}/{$this->tasks} - {$this->elapsed()}";
        fwrite(STDOUT, "\r" . $line);
        $this->rendered = true;
    }
}
