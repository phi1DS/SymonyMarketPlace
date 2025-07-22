namespace App\Tests\Controller;

use PHPUnit\Framework\TestCase;
use App\Service\Calculator;

class CalculatorTest extends TestCase
{
    public function testAdd()
    {
        $calc = new Calculator();
        $this->assertEquals(5, $calc->add(2, 3));
    }
}
