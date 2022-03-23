#!/usr/bin/env php
<?php
// foobar.php.
namespace Command;

/*  p.s. my experience tells me that code optimization is not always the most important task. Sometimes it's more important
    to finish the task faster than spending time optimizing the script (except memory leaks or too obvious optimization mistakes).
    Server capacity is much cheaper than developer time. I think there is must be a compromise between the ease of understanding
    the code and its optimization. Of course, there are specific tasks when it becomes important to save resources (for example:
    mobile devices, regular processing of large amounts of data, speeding up web page loading by a significant number of milliseconds).

    Database
    Most often, the optimization of sql queries is a much more important. For example, sometimes it is worth storing redundant data
    in the database in order not to do heavy "joins". It is not always useful to create many indexes, because they slow down
    the process of adding new data to the table. It all depends on how often data is added or selected.
    My conclusions are based on personal experience of working on specific tasks.
*/

// Logic task has no conditions about memory limit or other optimization
// So there will be several solutions


// most obvious to me solution is to fill a result array and print data comma separated
$result = [];
for ($i = 1; $i <= 100; $i++) {
    if ($i % 3 === 0 && $i % 5 === 0) { // same as divisible by 15 but it might be not obvious to other developers
        $result[] = 'foobar';
    } elseif ($i % 3 === 0) {
        $result[] = 'foo';
    } elseif ($i % 5 === 0) {
        $result[] = 'bar';
    } else {
        $result[] = (string)$i; // for the same type of data, for example to use as json output
    }
}
echo implode(', ', $result) . "\n\n\n\n";


// less memory but less flexible
$max = 100;
for ($i = 1; $i <= $max; $i++) {
    if ($i % 15 === 0) {
        echo 'foobar';
    } elseif ($i % 3 === 0) {
        echo 'foo';
    } elseif ($i % 5 === 0) {
        echo 'bar';
    } else {
        echo $i;
    }
    if ($i < $max) { // in case if comma is required. If space delimiter is enough easier to add space to the end of each echo
        echo ', ';
    }
}
echo "\n\n\n\n";


// just for fun :)
$requiredSize = 100;
$maxIterations = $requiredSize * 5;
$result = [];
$iteration = 0;
while (true) {
    if ($iteration > $maxIterations) {
        echo 'You\'re out of luck, keep trying';
        break;
    }
    $number = rand(1, $requiredSize);
    if (!isset($result[$number])) {
        $result[$number] = ($number % 15 === 0 ? 'foobar' : ($number % 3 === 0 ? 'foo' : ($number % 5 === 0 ? 'bar' : (string)$number)));
    }
    if (count($result) === $requiredSize) {
        break;
    }
    $iteration++;
}
if (count($result) === $requiredSize) {
    ksort($result);
    echo implode(', ', array_values($result));
}
echo "\n\n\n\n";


// crazy method
// here should be the code that uses curl and online random number generation service



