<?php

namespace Tests\Unit\Http\Requests;

use Tests\TestCase;

/**
 * @see \App\Http\Requests\AddResourcesAdminRequest
 */
class AddResourcesAdminRequestTest extends TestCase
{
    /** @var \App\Http\Requests\AddResourcesAdminRequest */
    private $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new \App\Http\Requests\AddResourcesAdminRequest();
    }

    /**
     * @test
     */
    public function rules(): void
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $actual = $this->subject->rules();

        $this->assertValidationRules([
            'fileCategoryNew' => [
                'required',
            ],
            'fileNameNew' => [
                'required',
                'string',
                'max:50',
            ],
            'fileDescriptionNew' => [
                'required',
                'string',
                'max:500',
            ],
            'fileTypeNew' => [
                'required',
            ],
            'fileVersionNew' => [
                'nullable',
                'string',
                'max:25',
            ],
            'LinkNew' => [
                'nullable',
                'string',
                'max:255',
            ],
            'filePathNew' => [
                'nullable',
                'string',
                'max:255',
            ],
        ], $actual);
    }

    // test cases...
}
