<?php

use \Themosis\Validation\ValidationBuilder;

class ValidationTest extends PHPUnit_Framework_TestCase
{
    protected $validator;

    public function setUp()
    {
        $this->validator = new ValidationBuilder();
    }

    public function testSingle()
    {
        $input = 'username';
        $v = $this->validator->single($input, ['min:3', 'max:8']);
        $this->assertSame($v, $input);

        $input = 'username1243';
        $v = $this->validator->single($input, ['min:3', 'max:8']);
        $this->assertEmpty($v);
    }

    public function testSingleIsEmptyWithEmptyInputString()
    {
        $v = $this->validator->single('', ['alpha']);
        $this->assertEmpty($v);
    }

    public function testSingleIsEmptyWithEmptyInputArray()
    {
        $v = $this->validator->single([], ['alpha']);
        $this->assertEmpty($v);
    }

    public function testSingleIsSameValueWithEmptyRuleArray()
    {
        $v = $this->validator->single('something', []);
        $this->assertEquals('something', $v);
    }

    public function testMultiple()
    {
        $input = [
            'username' => 'username12',
            'email'    => 'user@domain.com'
        ];

        $rules = [
            'username' => ['max:10', 'alnum'],
            'email'    => ['email'],
        ];

        $v = $this->validator->multiple($input, $rules);
        $this->assertSame($v, $input);

        $rules = [
            'username' => ['max:5', 'alnum'],
            'email'    => ['email'],
        ];

        $v = $this->validator->multiple($input, $rules);
        $this->assertEmpty($v['username']);
    }

    public function testMultipleFailWithEmptyInputArray()
    {
        $v = $this->validator->multiple([], []);
        $this->assertEmpty($v);
    }

    public function testMultipleFailWithEmptyRuleArray()
    {
        $input = [
            'username' => 'username12',
            'email' => 'user@domain.com'
        ];

        $v = $this->validator->multiple($input, []);
        $this->assertEquals([], $v);
    }

    public function testIsAssociative()
    {
        $associative = $this->validator->isAssociative(['oh' => 'yes']);
        $this->assertTrue($associative, 'Passing associative array');

        $empty = $this->validator->isAssociative([]);
        $this->assertFalse($empty, 'Passing empty array');

        $indexed = $this->validator->isAssociative(['nope']);
        $this->assertFalse($indexed, 'Passing indexed array');
    }

    public function testValidateAlpha()
    {
        $v = $this->validator->single('username', ['alpha']);
        $this->assertSame('username', $v);

        $v = $this->validator->single('username123', ['alpha']);
        $this->assertSame('', $v);

        $v = $this->validator->single('123', ['alpha']);
        $this->assertSame('', $v);
    }

    public function testValidateNum()
    {
        $numbers  = ['10002', '1820.20', '-10002', 'wsl!12'];
        $expected = ['10002', '', '', ''];

        $v = $this->validator->single($numbers, ['num']);
        $this->assertSame($expected, $v);
    }

    public function testValidateNegNum()
    {
        $numbers  = ['-10002', '0', '-0', '10002', 'wsl!12'];
        $expected = ['-10002', '', '', '', ''];

        $v = $this->validator->single($numbers, ['negnum']);
        $this->assertSame($expected, $v);
    }

    public function testValidateAlNum()
    {
        $v = $this->validator->single('username', ['alnum']);
        $this->assertSame('username', $v);

        $v = $this->validator->single('username123', ['alnum']);
        $this->assertSame('username123', $v);

        $v = $this->validator->single('123', ['alnum']);
        $this->assertSame('123', $v);

        $v = $this->validator->single('user_name', ['alnum']);
        $this->assertSame('', $v);
    }

    public function testValidateTextfield()
    {
        $input = '<p>This is a sentence.</p> <script>beEvil();</script>';
        $result = 'This is a sentence.';

        $v = $this->validator->single($input, ['textfield']);
        $this->assertSame($result, $v);
    }

    public function testValidateTextarea()
    {
        $input = '<p>This is a sentence.</p> <script>beEvil();</script>';
        $result = '&lt;p&gt;This is a sentence.&lt;/p&gt; &lt;script&gt;beEvil();&lt;/script&gt;';

        $v = $this->validator->single($input, ['textarea']);
        $this->assertSame($result, $v);
    }

    public function testValidateHtml()
    {
        $input = '<p>This is a sentence.</p> <script>beEvil();</script>';
        $result = '&lt;p&gt;This is a sentence.&lt;/p&gt; &lt;script&gt;beEvil();&lt;/script&gt;';

        $v = $this->validator->single($input, ['html']);
        $this->assertSame($result, $v);
    }

    public function testValidateEmail()
    {
        // Valid email
        $validEmail = 'email@domain.com';
        $v = $this->validator->single($validEmail, ['email']);

        $this->assertSame($validEmail, $v);

        // Invalid email
        $invalidEmail = '@email.com';
        $v = $this->validator->single($invalidEmail, ['email']);

        $this->assertEmpty($v);
    }

    public function testValidateUrl()
    {
        $url  = 'http://domain.com';
        $url2 = 'https://domain.com';
        $url3 = 'https://domain.com/?foo=1&bar=2';

        $returns = [
            $url,
            $url2,
            'https://domain.com/?foo=1&#038;bar=2'
        ];

        $v = $this->validator->single([$url, $url2, $url3], ['url']);
        $this->assertSame($returns, $v);

        $v = $this->validator->single($url, ['url:https']);
        $this->assertEmpty($v);
    }

    public function testValidateMin()
    {
        $input = 'username';

        $v = $this->validator->single($input, ['min:0']);
        $this->assertSame($input, $v);

        $v = $this->validator->single($input, ['min:-1']);
        $this->assertSame($input, $v);

        $v = $this->validator->single($input, ['min:20']);
        $this->assertEmpty($v);
    }

    public function testValidateMax()
    {
        $input = 'username';

        $v = $this->validator->single($input, ['max:0']);
        $this->assertEmpty($v);

        $v = $this->validator->single($input, ['max:-1']);
        $this->assertEmpty($v);

        $v = $this->validator->single('this string is ♫ ♫ ♫ ♪ ♪ ♫', ['max:20']);
        $this->assertEmpty($v);

        $v = $this->validator->single('Some string', ['max:15']);
        $this->assertSame('Some string', $v);
    }

    public function testValidateBool()
    {
        $input =    ['1', 'on', 'yes', 'true', 'TRUE', true, '11', null, false, 0, 'string'];
        $expected = ['1', 'on', 'yes', 'true', 'TRUE', true, '', '', '', '', ''];

        $v = $this->validator->single($input, ['bool']);
        $this->assertSame($expected, $v);
    }

    public function testValidateKses()
    {
        $expected  = '<a href="index.html" title="Home">Home</a> <p>Lorem</p> Evil Script';
        $expected2 = '<a>Home</a> Lorem Evil Script';
        $expected3 = 'Home Lorem Evil Script';

        $html = '<a href="index.html" title="Home">Home</a> <p>Lorem</p> <script>Evil Script</script>';

        $v  = $this->validator->single($html, ['kses:a']);
        $this->assertSame($v, $expected2);

        $v = $this->validator->single($html, ['kses:a|href|title, p']);
        $this->assertSame($v, $expected);

        $v = $this->validator->single($html, ['kses:|h1']);
        $this->assertSame($v, $expected3);

        $v = $this->validator->single($html, ['kses']);
        $this->assertEmpty($v);
    }

    public function testValidateHex()
    {
        $values = ['AB10BC99', 'AR1012', 'ab12bc99', true, 'string'];
        $expected = ['AB10BC99', '', 'ab12bc99', '', ''];

        $v = $this->validator->single($values, ['hex']);
        $this->assertSame($expected, $v);
    }

    public function testValidateColor()
    {
        $rules = ['color'];

        $color  = '#000';
        $v = $this->validator->single($color, $rules);
        $this->assertSame($color, $v);

        $color = '#000aaa';
        $v = $this->validator->single($color, $rules);
        $this->assertSame($color, $v);

        $color = '#000aaabbb';
        $v = $this->validator->single($color, $rules);
        $this->assertEmpty($v);

        $color = '#';
        $v = $this->validator->single($color, $rules);
        $this->assertEmpty($v);

        $color = '000';
        $v = $this->validator->single($color, $rules);
        $this->assertEmpty($v);

        $color = '#xxxxxx';
        $v = $this->validator->single($color, $rules);
        $this->assertEmpty($v);
    }

    public function testValidateFile()
    {
        $file = 'someFileName.jpg';

        $v = $this->validator->single($file, ['file:jpg']);
        $this->assertSame($file, $v);

        $v = $this->validator->single($file, ['file:png']);
        $this->assertEmpty($v);
    }

    public function testValidateRequired()
    {
        $input = 'username';
        $rules = ['required'];

        $v = $this->validator->single($input, $rules);
        $this->assertSame($input, $v);

        $v = $this->validator->single('', $rules);
        $this->assertEmpty($v);
    }
}
