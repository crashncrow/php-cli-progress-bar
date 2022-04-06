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

    public function __construct( $name, $tasks, $processed = 0 ) {
        $this->start = microtime(true);
        $this->name = $name;
        $this->tasks = $tasks;
        $this->processed = $processed;
        $this->render();
    }

    public function increase( $increment = 1 ) {
        if ($this->processed < $this->tasks) {
            $this->processed = min($this->processed + $increment, $this->tasks);
            $this->render();
        }
        else {
            fwrite( STDOUT, PHP_EOL );
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
        if ($show) {
            fwrite(STDOUT, "\n");
            fwrite(STDOUT, PHP_EOL);

            if ($beer) {
                fwrite(STDOUT, "\xF0\x9F\x8D\xBA ");
            }

            fwrite(STDOUT, "END {$this->name}" . PHP_EOL);
        }
        fwrite(STDOUT, "\n");
    }

    private function map( $value, $fromLow, $fromHigh, $toLow, $toHigh ) {
        $fromRange = $fromHigh - $fromLow;
        $toRange = $toHigh - $toLow;
        $scaleFactor = $toRange / $fromRange;

        // Re-zero the value within the from range
        $tmpValue = $value - $fromLow;
        // Rescale the value to the to range
        $tmpValue *= $scaleFactor;
        // Re-zero back to the to range
        return $tmpValue + $toLow;
    }

    private function render() {
        // Save position
        fwrite( STDOUT, "\0337" );

        // Restore position
        fwrite( STDERR, "\0338" );

        $step = (int)$this->map($this->processed, 0, $this->tasks, 0, $this->size);
        $progress = min((int)($this->processed * 100 / $this->tasks), 100);
        $progress = str_pad($progress, 3, ' ', STR_PAD_LEFT);

        // Write progress bar
        fwrite( STDERR, "[\033[32m" . str_repeat('#', $step) . str_repeat('·', $this->size - $step) . "\033[0m]" );
        fwrite( STDOUT, " {$progress}% Complete - {$this->name} - {$this->processed}/{$this->tasks} - {$this->elapsed()}" . PHP_EOL );

        // Move up, undo the PHP_EOL
        fwrite( STDERR, "\033[1A" );
    }
}
