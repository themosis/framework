<?php

use \WP_Mock as WP;
use \Themosis\Validation\ValidationBuilder;

class ValidationBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function setUp() {
        WP::setUp();
    }

    public function tearDown() {
        WP::tearDown();
    }

    public function testSingle()
    {
        $validator = new ValidationBuilder();

        $input = 'username';
        $v = $validator->single($input, ['min:3', 'max:8']);
        $this->assertSame($v, $input);

        $input = 'username1243';
        $v = $validator->single($input, ['min:3', 'max:8']);
        $this->assertEmpty($v);
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testSingleFailWithEmptyInputString()
    {
        $validator = new ValidationBuilder();

        // TODO: Validates without any errors, maybe it should not?
        $validator->single('', ['alpha']);
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testSingleFailWithEmptyInputArray()
    {
        $validator = new ValidationBuilder();

        // TODO: Validates without any errors, maybe it should not?
        $validator->single([], ['alpha']);
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testSingleFailWithEmptyRuleArray()
    {
        // TODO: This should fail or throw an error but it doesn't do either
        $validator = new ValidationBuilder();
        $validator->single('something', []);
    }

    public function testMultiple()
    {
        $validator = new ValidationBuilder();

        $input = [
            'username' => 'username12',
            'email'    => 'user@domain.com'
        ];

        WP::wpFunction('sanitize_email', [
            'times' => 2,
            'return' => $input['email']
        ]);

        WP::wpFunction('is_email', [
            'times' => 2,
            'return' => $input['email']
        ]);

        $rules = [
            'username' => ['max:10', 'alnum'],
            'email'    => ['email'],
        ];

        $v = $validator->multiple($input, $rules);
        $this->assertSame($v, $input);

        $rules = [
            'username' => ['max:5', 'alnum'],
            'email'    => ['email'],
        ];

        $v = $validator->multiple($input, $rules);
        $this->assertEmpty($v['username']);
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testMultipleFailWithEmptyInputArray()
    {
        $validator = new ValidationBuilder();

        // TODO: Validates without any errors, maybe it should not?
        $validator->multiple([], []);
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testMultipleFailWithEmptyRuleArray()
    {
        // TODO: This should fail or throw an error but it doesn't do either
        $validator = new ValidationBuilder();

        $input = [
            'username' => 'username12',
            'email'    => 'user@domain.com'
        ];

        $validator->multiple($input, []);
    }

    public function testIsAssociative()
    {
        $validator = new ValidationBuilder();

        $associative = $validator->isAssociative(['oh' => 'yes']);
        $this->assertTrue($associative, 'Passing associative array');

        // TODO: If array is empty returns true but it should be false
        $empty = $validator->isAssociative([]);
        $this->assertFalse($empty, 'Passing empty array');

        $indexed = $validator->isAssociative(['nope']);
        $this->assertFalse($indexed, 'Passing indexed array');
    }

    public function testValidateAlpha()
    {
        $validator = new ValidationBuilder();

        $v = $validator->single('username', ['alpha']);
        $this->assertSame('username', $v);

        $v = $validator->single('username123', ['alpha']);
        $this->assertSame('', $v);

        $v = $validator->single('123', ['alpha']);
        $this->assertSame('', $v);
    }

    public function testValidateNum()
    {
        $validator = new ValidationBuilder();

        $numbers  = ['10002', '1820.20', '-10002', 'wsl!12'];
        $expected = ['10002', '', '', ''];

        $v = $validator->single($numbers, ['num']);
        $this->assertSame($expected, $v);
    }

    public function testValidateNegNum()
    {
        $validator = new ValidationBuilder();

        $numbers  = ['-10002', '0', '-0', '10002', 'wsl!12'];
        $expected = ['-10002', '', '', '', ''];

        $v = $validator->single($numbers, ['negnum']);
        $this->assertSame($expected, $v);
    }

    public function testValidateAlNum()
    {
        $validator = new ValidationBuilder();

        $v = $validator->single('username', ['alnum']);
        $this->assertSame('username', $v);

        $v = $validator->single('username123', ['alnum']);
        $this->assertSame('username123', $v);

        $v = $validator->single('123', ['alnum']);
        $this->assertSame('123', $v);

        $v = $validator->single('user_name', ['alnum']);
        $this->assertSame('', $v);
    }

    public function testValidateTextfield()
    {
        $validator = new ValidationBuilder();

        $input = '<p>This is a sentence.</p> <script>beEvil();</script>';
        $result = 'This is a sentence.';

        WP::wpFunction('sanitize_text_field', [
            'times' => 1,
            'return' => $result
        ]);

        $v = $validator->single($input, ['textfield']);
        $this->assertSame($result, $v);
    }

    public function testValidateTextarea()
    {
        $validator = new ValidationBuilder();

        $input = '<p>This is a sentence.</p> <script>beEvil();</script>';
        $result = '&lt;p&gt;This is a sentence.&lt;/p&gt; &lt;script&gt;beEvil();&lt;/script&gt;';

        WP::wpFunction('esc_textarea', [
            'times' => 1,
            'return' => $result
        ]);

        $v = $validator->single($input, ['textarea']);
        $this->assertSame($result, $v);
    }

    public function testValidateHtml()
    {
        $validator = new ValidationBuilder();

        $input = '<p>This is a sentence.</p> <script>beEvil();</script>';
        $result = '&lt;p&gt;This is a sentence.&lt;/p&gt; &lt;script&gt;beEvil();&lt;/script&gt;';

        WP::wpFunction('esc_html', [
            'times' => 1,
            'return' => $result
        ]);

        $v = $validator->single($input, ['html']);
        $this->assertSame($result, $v);
    }

    public function testValidateEmail()
    {
        $validator = new ValidationBuilder();

        // Valid email
        $validEmail = 'email@domain.com';
        WP::wpFunction('sanitize_email', [
            'times' => 2,
            'return' => true
        ]);

        WP::wpFunction('is_email', [
            'times' => 1,
            'return' => $validEmail
        ]);

        $isEmail = $validator->single($validEmail, ['email']);

        $this->assertTrue($isEmail);

        // Invalid email
        $invalidEemail = '@email.com';

        WP::wpFunction('is_email', [
            'times' => 1,
            'return' => false
        ]);

        $isNotEmail = $validator->single($invalidEemail, ['email']);

        $this->assertEmpty($isNotEmail);
    }

    public function testValidateUrl()
    {
        $validator = new ValidationBuilder();

        $url  = 'http://domain.com';
        $url2 = 'https://domain.com';
        $url3 = 'https://domain.com/?foo=1&bar=2';

        $returns = [
            $url,
            $url2,
            'https://domain.com/?foo=1&#038;bar=2'
        ];

        WP::wpFunction('esc_url', [
            'times' => 3,
            'return_in_order' => $returns
        ]);

        $v = $validator->single([$url, $url2, $url3], ['url']);
        $this->assertSame($returns, $v);

        WP::wpFunction('esc_url', [
            'times' => 1,
            'return' => ''
        ]);

        $v = $validator->single($url, ['url:https']);
        $this->assertSame('', $v);
    }

    public function testValidateMin()
    {
        $validator = new ValidationBuilder();

        $input = 'username';

        $v = $validator->single($input, ['min:0']);
        $this->assertSame($input, $v);

        $v = $validator->single($input, ['min:-1']);
        $this->assertSame($input, $v);

        $v = $validator->single($input, ['min:20']);
        $this->assertSame('', $v);
    }

    public function testValidateMax()
    {
        $validator = new ValidationBuilder();

        $input = 'username';

        $v = $validator->single($input, ['max:0']);
        $this->assertSame('', $v);

        $v = $validator->single($input, ['max:-1']);
        $this->assertSame('', $v);

        $v = $validator->single('this string is ♫ ♫ ♫ ♪ ♪ ♫', ['max:20']);
        $this->assertSame('', $v);
    }

    public function testValidateBool()
    {
        $validator = new ValidationBuilder();

        $input =    ['1', 'on', 'yes', 'true', 'TRUE', TRUE, '11', null, false, 0,  'string'];
        $expected = ['1', 'on', 'yes', 'true', 'TRUE', true, '',   '',   '',    '', ''];

        $v = $validator->single($input, ['bool']);

        $this->assertSame($expected, $v);
    }

    public function testValidateKses()
    {
        $validator = new ValidationBuilder();

        $expected  = '<a href="index.html" title="Home">Home</a> <p>Lorem</p> Evil Script';
        $expected2 = '<a>Home</a> Lorem Evil Script';
        $expected3 = 'Home Lorem Evil Script';

        WP::wpFunction('wp_kses', [
            'times' => 3,
            'return_in_order' => [$expected, $expected2, $expected3]
        ]);

        $html = '<a href="index.html" title="Home">Home</a> <p>Lorem</p> <script>Evil Script</script>';

        $v  = $validator->single($html, ['kses:a']);
        $this->assertSame($v, $expected);

        $v = $validator->single($html, ['kses:a|href|title, p']);
        $this->assertSame($v, $expected2);

        $v = $validator->single($html, ['kses:|h1']);
        $this->assertSame($v, $expected3);

        $v = $validator->single($html, ['kses']);
        $this->assertEmpty($v);
    }

    public function testValidateHex()
    {
        $validator = new ValidationBuilder();

        $values = ['AB10BC99', 'AR1012', 'ab12bc99', true, 'string'];
        $expected = ['AB10BC99', '', 'ab12bc99', '', ''];

        $v = $validator->single($values, ['hex']);
        $this->assertSame($expected, $v);
    }

    public function testValidateColor()
    {
        $validator = new ValidationBuilder();

        $rules = ['color'];

        $color  = '#000';
        $v = $validator->single($color, $rules);
        $this->assertSame($color, $v);

        $color = '#000aaa';
        $v = $validator->single($color, $rules);
        $this->assertSame($color, $v);

        $color = '#000aaabbb';
        $v = $validator->single($color, $rules);
        $this->assertEmpty($v);

        $color = '#';
        $v = $validator->single($color, $rules);
        $this->assertEmpty($v);

        $color = '000';
        $v = $validator->single($color, $rules);
        $this->assertEmpty($v);

        $color = '#xxxxxx';
        $v = $validator->single($color, $rules);
        $this->assertEmpty($v);
    }

    public function testValidateFile()
    {
        $validator = new ValidationBuilder();

        $file = 'file.jpg';

        $v = $validator->single($file, ['file:jpg']);
        $this->assertSame($file, $v);

        $v = $validator->single($file, ['file:png']);
        $this->assertEmpty($v);
    }

    public function testValidateRequired()
    {
        $validator = new ValidationBuilder();

        $input = 'username';
        $rules = ['required'];

        $v = $validator->single($input, $rules);
        $this->assertSame($input, $v);

        $v = $validator->single('', $rules);
        $this->assertEmpty($v);
    }
}
