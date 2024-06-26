<?php

namespace Tests\Unit\Http\Requests;

use Tests\TestCase;

/**
 * @see \App\Http\Requests\StoreAward4GoogleRequest
 */
class StoreAward4GoogleRequestTest extends TestCase
{
    /** @var \App\Http\Requests\StoreAward4GoogleRequest */
    private $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new \App\Http\Requests\StoreAward4GoogleRequest();
    }

    /**
     * @test
     */
    public function rules(): void
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $actual = $this->subject->rules();

        $this->assertValidationRules([
            'file' => [
                'required',
            ],
        ], $actual);
    }

    // test cases...
}
