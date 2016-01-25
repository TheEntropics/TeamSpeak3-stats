<?php


class FenwickTree {
    private $ft1;
    private $ft2;
    private $size;

    /**
     * Prepare the Fenwick tree structure
     * @param $size The size of the fenwick tree
     */
    public function __construct($size) {
        $this->size = $size;
        $this->ft1 = new SplFixedArray($size);
        $this->ft2 = new SplFixedArray($size);
        for ($i = 0; $i < $size; $i++)
            $this->ft1[$i] = $this->ft2[$i] = 0;
    }

    /**
     * Add $delta to the 0-based range [ $from, $to ]
     */
    public function rangeUpdate($from, $to, $delta) {
        // convert to 1-based
        $from++; $to++;

        $this->pointUpdate($this->ft1, $from, $delta);
        $this->pointUpdate($this->ft1, $to + 1, -$delta);
        $this->pointUpdate($this->ft2, $from, $delta*($from-1));
        $this->pointUpdate($this->ft2, $to+1, -$delta*$to);
    }

    /**
     * Return the sum of the range [ $from, $to ]
     */
    public function rangeQuery($from, $to) {
        return $this->prefixQuery($to+1) - $this->prefixQuery($from+1-1);
    }

    private function pointUpdate($ft, $i, $delta) {
        while ($i < $this->size) {
            $ft[$i] += $delta;
            $i += $i & -$i;
        }
    }

    private function pointQuery($ft, $i) {
        $sum = 0;
        while ($i > 0) {
            $sum += $ft[$i];
            $i -= $i & -$i;
        }
        return $sum;
    }
    private function prefixQuery($i) {
        return $this->pointQuery($this->ft1, $i) * $i - $this->pointQuery($this->ft2, $i);
    }
}
