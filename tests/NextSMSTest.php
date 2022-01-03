<?php

namespace Omakei\NextSMS\Tests;

use Illuminate\Support\Facades\Http;
use Mockery;
use Omakei\NextSMS\Exceptions\InvalidPayload;
use Omakei\NextSMS\NextSMS;

class NextSMSTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();

        Mockery::close();
    }

    /** @test */
    public function it_can_send_single_sms_to_single_destination()
    {
        $stub = json_decode(
            file_get_contents(__DIR__ . '/stubs/responses/single_destination.json'),
            true
        );

        Http::fake([config('nextsms.url.sms.single') => Http::response($stub, 200)]);

        $response = NextSMS::sendSingleSMS(['to' => '25576328997', 'text' => 'Dj Omakei is texting.']);


        $this->assertEquals($response->json(), $stub);
    }

    /** @test */
    public function it_can_throw_exception_when_payload_of_single_destination_is_invalid()
    {
        $this->expectException(InvalidPayload::class);

        $this->expectExceptionMessage('The payload provided must contain "to" and "text" keys.');

        $response = NextSMS::sendSingleSMS(['to' => '25576328997']);
    }

    /** @test */
    public function it_can_send_single_sms_to_multiple_destination()
    {
        $stub = json_decode(
            file_get_contents(__DIR__ . '/stubs/responses/multiple_destination.json'),
            true
        );

        Http::fake([config('nextsms.url.sms.single') =>
            Http::response($stub, 200), ]);

        $response = NextSMS::sendSingleSMSToMultipleDestination([
                    'to' => ['25576328997'],
                    'text' => 'Dj Omakei is texting.', ]);

        $this->assertEquals($response->json(), $stub);
    }

    /** @test */
    public function it_can_throw_exception_when_payload_of_multiple_destination_is_invalid()
    {
        $this->expectException(InvalidPayload::class);

        $this->expectExceptionMessage('The payload provided must contain "to" key of type array.');

        $response = NextSMS::sendSingleSMSToMultipleDestination([
            'to' => '25576328997',
            'text' => 'Dj Omakei is texting.', ]);
    }

    /** @test */
    public function it_can_send_multiple_sms_to_multiple_destination()
    {
        $stub = json_decode(
            file_get_contents(__DIR__ . '/stubs/responses/multiple_sms_to_multiple_destination.json'),
            true
        );

        Http::fake([config('nextsms.url.sms.multiple') =>
            Http::response($stub, 200), ]);

        $response = NextSMS::sendMultipleSMSToMultipleDestinations(['messages' => [[
                'to' => '25576328997',
                'text' => 'Dj Omakei is texting.', ]]]);

        $this->assertEquals($response->json(), $stub);
    }

    /** @test */
    public function it_can_throw_exception_when_payload_of_multiple_sms_to_multiple_destination_is_invalid()
    {
        $this->expectException(InvalidPayload::class);

        $this->expectExceptionMessage('The payload provided must contain "to" and "text" keys.');

        $response = NextSMS::sendMultipleSMSToMultipleDestinations(['messages' => [
            ['to' => '25576328997'], ]]);
    }

    /** @test */
    public function it_can_schedule_sms()
    {
        $stub = json_decode(
            file_get_contents(__DIR__ . '/stubs/responses/schedule_sms.json'),
            true
        );

        Http::fake([config('nextsms.url.sms.single') =>
            Http::response($stub, 200), ]);

        $response = NextSMS::scheduleSMS([
            'to' => '25576328997',
            'text' => 'Dj Omakei is texting.',
            'date' => '2022-01-25' ,
            'time' => '12:00',
            ]);

        $this->assertEquals($response->json(), $stub);
    }

    /** @test */
    public function it_can_throw_exception_when_date_payload_of_schedule_sms_is_invalid()
    {
        $this->expectException(InvalidPayload::class);

        $this->expectExceptionMessage('The payload provided must contain "date" key of format "YYYY-MM-DD".');

        $response = NextSMS::scheduleSMS([
            'to' => '25576328997',
            'text' => 'Dj Omakei is texting.',
            'date' => '2022/01/25' ,
            'time' => '12:00',
        ]);
    }

    /** @test */
    public function it_can_throw_exception_when_time_payload_of_schedule_sms_is_invalid()
    {
        $this->expectException(InvalidPayload::class);

        $this->expectExceptionMessage('The payload provided must contain "time" key of format "HH:MM".');

        $response1 = NextSMS::scheduleSMS([
            'to' => '25576328997',
            'text' => 'Dj Omakei is texting.',
            'date' => '2022-01-25' ,
            'time' => '12:90',
        ]);
    }

    /** @test */
    public function it_can_get_all_delivery_reports()
    {
        $stub = json_decode(
            file_get_contents(__DIR__ . '/stubs/responses/recharge_sub_customer.json'),
            true
        );

        Http::fake([config('nextsms.url.sms.reports') =>
            Http::response($stub, 200), ]);

        $response = NextSMS::getAllDeliveryReports();

        $this->assertEquals($response->json(), $stub);
    }

    /** @test */
    public function it_can_delivery_report_with_message_id()
    {
        $stub = json_decode(
            file_get_contents(__DIR__ . '/stubs/responses/delivery_reports_with_message_id.json'),
            true
        );

        Http::fake([config('nextsms.url.sms.single') =>
            Http::response($stub, 200), ]);

        $response = NextSMS::getDeliveryReportWithMessageId(2346673573733);

        $this->assertNotEquals($response->json(), $stub);
    }

    /** @test */
    public function it_can_get_delivery_report_with_specific_date_range()
    {
        $stub = json_decode(
            file_get_contents(__DIR__ . '/stubs/responses/delivery_report_with_specific_date_range.json'),
            true
        );

        Http::fake([config('nextsms.url.sms.reports') =>
            Http::response($stub, 200), ]);

        $response = NextSMS::getDeliveryReportWithSpecificDateRange('2022-01-25', '2022-01-29');

        $this->assertNotEquals($response->json(), $stub);
    }

    /** @test */
    public function it_can_throw_exception_when_arguments_of_get_delivery_report_with_specific_date_range_is_invalid()
    {
        $this->expectException(InvalidPayload::class);

        $this->expectExceptionMessage('The payload provided must contain "date" key of format "YYYY-MM-DD".');

        $response = NextSMS::getDeliveryReportWithSpecificDateRange('2022/01/25', '2022/01/29');
    }

    /** @test */
    public function it_can_get_all_sent_sms_logs()
    {
        $stub = json_decode(
            file_get_contents(__DIR__ . '/stubs/responses/all_sent_sms_logs.json'),
            true
        );

        Http::fake([config('nextsms.url.sms.reports') =>
            Http::response($stub, 200), ]);

        $response = NextSMS::getAllSentSMSLogs(20, 5);

        $this->assertNotEquals($response->json(), $stub);
    }

    /** @test */
    public function it_can_get_all_sent_sms_logs_with_optional_parameter()
    {
        $stub = json_decode(
            file_get_contents(__DIR__ . '/stubs/responses/all_sent_sms_logs.json'),
            true
        );

        Http::fake([config('nextsms.url.sms.reports').'/*' =>
            Http::response($stub, 200), ]);

        $response = NextSMS::getAllSentSMSLogsWithOptionalParameter(
            '255625933171',
            '2022-01-25',
            '2022-01-29',
            10,
            5
        );

        $this->assertNotEquals($response->json(), $stub);
    }

    /** @test */
    public function it_can_throw_exception_when_arguments_of_get_all_sent_sms_logs_with_optional_parameter_is_invalid()
    {
        $this->expectException(InvalidPayload::class);

        $this->expectExceptionMessage('The payload provided must contain "date" key of format "YYYY-MM-DD".');

        $response = NextSMS::getAllSentSMSLogsWithOptionalParameter(
            '255625933171',
            '2022/01/25',
            '2022/01/29',
            10,
            5
        );
    }

    /** @test */
    public function it_can_get_sms_balance()
    {
        $stub = json_decode(
            file_get_contents(__DIR__ . '/stubs/responses/get_sms_balance.json'),
            true
        );

        Http::fake([config('nextsms.url.sms.balance') =>
            Http::response($stub, 200), ]);

        $response = NextSMS::getSMSBalance();

        $this->assertEquals($response->json(), $stub);
    }

    /** @test */
    public function it_can_register_sub_customer()
    {
        $stub = json_decode(
            file_get_contents(__DIR__ . '/stubs/responses/create_sub_customer.json'),
            true
        );

        Http::fake([config('nextsms.url.sub_customer.create') =>
            Http::response($stub, 200), ]);

        $response = NextSMS::subCustomerCreate(
            'Michael',
            'Omakei',
            'omakei',
            'omakei96@gmail.com',
            '06259313171',
            'Sub Customer (Reseller)',
            100
        );

        $this->assertEquals($response->json(), $stub);
    }

    /** @test */
    public function it_can_throw_exception_when_arguments_of_register_sub_customer_is_invalid()
    {
        $this->expectException(InvalidPayload::class);

        $this->expectExceptionMessage('The account type must be Sub Customer or Sub Customer (Reseller).');

        $response = NextSMS::subCustomerCreate(
            'Michael',
            'Omakei',
            'omakei',
            'omakei96@gmail.com',
            '06259313171',
            'Sub Customer (Omakei)',
            100
        );
    }

    /** @test */
    public function it_can_recharge_sub_customer()
    {
        $stub = json_decode(
            file_get_contents(__DIR__ . '/stubs/responses/recharge_sub_customer.json'),
            true
        );

        Http::fake([config('nextsms.url.sub_customer.recharge') =>
            Http::response($stub, 200), ]);

        $response = NextSMS::subCustomerRecharge('omakei96@gmail.com', 100);

        $this->assertEquals($response->json(), $stub);
    }

    /** @test */
    public function it_can_deduct_sub_customer()
    {
        $stub = json_decode(
            file_get_contents(__DIR__ . '/stubs/responses/deduct_sub_customer.json'),
            true
        );

        Http::fake([config('nextsms.url.sub_customer.deduct') =>
            Http::response($stub, 200), ]);

        $response = NextSMS::subCustomerDeduct('omakei96@gmail.com', 100);

        $this->assertEquals($response->json(), $stub);
    }
}
