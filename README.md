# PHP Cli Progress Bar
Simple progress bar for php cli

![php cli progress bar example](demo.gif)

## Usage

```php
$tasks = 5; // total tasks to run

$progress = new Progress("progress bar demo", $tasks);

for ($i = 0; $i < $tasks; $i++) {
    // do some task - start
    sleep(1);
    // do some task - end

    $progress->increase();
}

$progress->end();
```