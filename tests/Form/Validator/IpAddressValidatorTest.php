<?php

namespace MulerTech\MTerm\Tests\Form\Validator;

use MulerTech\MTerm\Form\Validator\IpAddressValidator;
use PHPUnit\Framework\TestCase;

class IpAddressValidatorTest extends TestCase
{
    public function testValidateWithValidIPv4(): void
    {
        $validator = new IpAddressValidator();
        $this->assertNull($validator->validate('192.168.1.1'));
        $this->assertNull($validator->validate('10.0.0.1'));
        $this->assertNull($validator->validate('172.16.0.1'));
        $this->assertNull($validator->validate('8.8.8.8'));
    }

    public function testValidateWithValidIPv6(): void
    {
        $validator = new IpAddressValidator();
        $this->assertNull($validator->validate('::1'));
        $this->assertNull($validator->validate('2001:0db8:85a3:0000:0000:8a2e:0370:7334'));
        $this->assertNull($validator->validate('2001:db8::1'));
        $this->assertNull($validator->validate('fe80::1'));
    }

    public function testValidateWithInvalidIPs(): void
    {
        $validator = new IpAddressValidator();
        $this->assertEquals(
            'Please enter a valid IP address.',
            $validator->validate('not-an-ip')
        );
        $this->assertEquals(
            'Please enter a valid IP address.',
            $validator->validate('256.256.256.256')
        );
        $this->assertEquals(
            'Please enter a valid IP address.',
            $validator->validate('192.168.1')
        );
        $this->assertEquals(
            'Please enter a valid IP address.',
            $validator->validate('::::::::')
        );
    }

    public function testValidateWithIPv4Only(): void
    {
        $validator = new IpAddressValidator(true, false);

        // IPv4 should be accepted
        $this->assertNull($validator->validate('192.168.1.1'));

        // IPv6 should be rejected
        $this->assertEquals(
            'Please enter a valid IP address.',
            $validator->validate('2001:db8::1')
        );
    }

    public function testValidateWithIPv6Only(): void
    {
        $validator = new IpAddressValidator(false, true);

        // IPv6 should be accepted
        $this->assertNull($validator->validate('2001:db8::1'));

        // IPv4 should be rejected
        $this->assertEquals(
            'Please enter a valid IP address.',
            $validator->validate('192.168.1.1')
        );
    }

    public function testValidateWithNoPrivateAddresses(): void
    {
        $validator = new IpAddressValidator(true, true, false);

        // Public IPv4 should be accepted
        $this->assertNull($validator->validate('8.8.8.8'));

        // Private IPv4 should be rejected
        $this->assertEquals(
            'Please enter a valid IP address.',
            $validator->validate('192.168.1.1')
        );
        $this->assertEquals(
            'Please enter a valid IP address.',
            $validator->validate('10.0.0.1')
        );

        // Private IPv6 should be rejected
        $this->assertEquals(
            'Please enter a valid IP address.',
            $validator->validate('fd00::1')
        );
    }

    public function testValidateWithNoReservedAddresses(): void
    {
        $validator = new IpAddressValidator(true, true, true, false);

        // Regular IPs should be accepted
        $this->assertNull($validator->validate('8.8.8.8'));

        // Reserved IPv4 addresses should be rejected
        $this->assertEquals(
            'Please enter a valid IP address.',
            $validator->validate('240.0.0.1')
        );
    }

    public function testValidateWithNullOrEmptyValue(): void
    {
        $validator = new IpAddressValidator();
        $this->assertNull($validator->validate(null));
        $this->assertNull($validator->validate(''));
    }

    public function testValidateWithCustomErrorMessage(): void
    {
        $customMessage = 'This is not a valid IP address format.';
        $validator = new IpAddressValidator(true, true, true, true, $customMessage);

        $this->assertEquals(
            $customMessage,
            $validator->validate('invalid-ip')
        );
    }

    public function testValidateWithMultipleRestrictions(): void
    {
        // IPv4 only and no private addresses
        $validator = new IpAddressValidator(true, false, false);

        // Public IPv4 should pass
        $this->assertNull($validator->validate('8.8.8.8'));

        // Private IPv4 should fail
        $this->assertEquals(
            'Please enter a valid IP address.',
            $validator->validate('192.168.1.1')
        );

        // Any IPv6 should fail (even public ones)
        $this->assertEquals(
            'Please enter a valid IP address.',
            $validator->validate('2001:4860:4860::8888')
        );
    }
}