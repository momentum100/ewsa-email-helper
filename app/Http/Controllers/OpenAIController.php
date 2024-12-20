<?php

namespace App\Http\Controllers;

use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Log;


class OpenAIController extends Controller
{
    public function categorizeEmail($contentForCategorization)
    {
        $modelName = 'o1-mini';

        $result = OpenAI::chat()->create([
            'model' => $modelName,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => "
                    As an assistant for processing incoming emails at a transportation company, analyze the provided email text and perform the following tasks:

Language Detection:

Identify the language in which the email is written.
Specify the language in English (e.g., 'Russian', 'English', 'Spanish').
Transportation Quote Request:

Determine if the email contains a request for a transportation quote.
Data Extraction and Verification:

Extract the provided data from the email and structure it accordingly.

Verify the completeness of the data based on the following requirements:

Required Fields:

Origin:
Must include the country and either the city or postal code (e.g., 'USA + New York' or 'USA + 10022').
Acceptable examples:

(country + city) + Postal Code: empty
Country: USA
City: New York
Postal code:

(country + city) + Postal code: empty
Country: POLAND
City: Warsaw
Postal code:

(country + postal code) + City: empty
Country: PL
City:
Postal code: 00697

(country CODE + city) + Postal Code: empty
Country: EST
City: Tallinn
Postal code:

(ICO country + postal code) + Postal Code: empty
Country: EST
City:
Postal code: 11417

Destination:
Must include the country and either the city or postal code.
Examples
(country + city) + Postal Code: empty
Country: USA
City: New York
Postal code:

(country + city) + Postal code: empty
Country: POLAND
City: Warsaw
Postal code:

(country + postal code) + City: empty
Country: PL
City:
Postal code: 00697

(country CODE + city) + Postal Code: empty
Country: EST
City: Tallinn
Postal code:

(ICO country + postal code) + Postal Code: empty
Country: EST
City:
Postal code: 11417


Cargo Name or HS Code:
A description of the cargo or its Harmonized System (HS) code (e.g., 'LED TV' or '09112.11').
Examples
Led TV
09111.21


Weight:
The weight of the cargo.

Dimensions or Volume:
Provide either the dimensions (length, width, height) or the volume of the cargo.
Acceptable answers


Pallets Quantity or LDM:
Specify either the number of pallets or the Load Meter (LDM).
Acceptable examples:
Pallets: 10 LDM: empty
Pallets: emtpy, LDM: 2

Pallets: 
LDM: 4

Pallets: 5
LDM: 



Optional Fields:

Loading Method: Specify if provided.
Transport Type: Specify if provided.
Departure Date or Month: Include if provided.


Classification and Action Determination:

    Classification:
    Categorize the email as either:
    'Price_Request': If it includes a request for a transportation quote.
    'General': If it does not include a request for a transportation quote.

    Action:
    Decide on one of the following actions based on the analysis:
    'Ignore': If there is no request for a quote.
    'Request additional data': If there is a quote request but the data is incomplete.
    'Add order to CRM': If there is a quote request and the data is complete.

Output:
- do not include any markup 
- never add markup like ```json ```html or anything else
- output strictly only json, so it can be processed futher automatically
Provide the answer in the following JSON format:

{
    \"language\": \"Email language\",
    \"category\": \"Price_Request\" or \"General\",
    \"contains_price_request\": true or false,
    \"data_complete\": true or false,
    \"provided_data\": {
    \"origin\": \"value or empty string\",
    \"destination\": \"value or empty string\",
    \"cargo_name_or_hs_code\": \"value or empty string\",
    \"weight\": \"value or empty string\",
    \"dimensions_or_volume\": \"value or empty string\",
    \"pallets_or_ldm\": \"value or empty string\",
    \"departure_date\": \"value or empty string\",
    \"loading_method\": \"value or empty string\",
    \"transport_type\": \"value or empty string\",
    \"cargo_value\": \"value or empty string\",
    \"currency\": \"value or empty string\"
  },
  \"action\": \"Ignore\" or \"Request additional data\" or \"Add order to CRM\"
}

                    EMAIL CONTENTS: {$contentForCategorization}"
                ],
            ],
        ]);


        Log::info('OpenAI API result:', ['result' => var_export($result, true)]);
        // Extract token usage from the result
        $usage = $result->usage ?? new \stdClass();
        $promptTokens = $usage->promptTokens ?? 0;
        $completionTokens = $usage->completionTokens ?? 0;

        // Calculate the cost
        $cost = $this->calculateApiRequestCost($modelName, $promptTokens, $completionTokens);
        Log::info('OpenAI API cost:', ['cost' => $cost]);

        // Return both the result and the cost
        return [
            'response' => $result,
            'cost' => $cost
        ];
    }

    public function generateMissingDataEmail($language, $providedData, $missingData, $contentForCategorization)
    {
        $modelName = 'o1-mini';

        $prompt = 'You are an assistant helping to compose an email requesting additional information from a client.

Initial Correspondence:
'. $contentForCategorization .'

Based on the initial correspondence, extract any provided information for the following fields:

- Route:
  - From:
    - Country
    - City (if provided)
    - Postal Code (if city is not provided)
    - *Note*: For the "From" location, we need either the country and city, or the country and postal code.
  - To:
    - Country
    - City (if provided)
    - Postal Code (if city is not provided)
    - *Note*: For the "To" location, we need either the country and city, or the country and postal code.

- Loading Date or Month
- Name or Code
- Cargo
- Loading Method (ask only if there\'s a requirement mentioned)
- Transport Type (use if specified; do not ask again)
- Cargo Value (ask only if mentioned)
- Currency (ask only if mentioned)
- Pallets: Quantity? (ask if pallets are mentioned)
- LDM (ask if pallets are not mentioned)

- Weight

- Dimensions:
  - Length: mandatory
  - Width: mandatory
  - Height: mandatory
  - *OR*
  - Volume (if dimensions are not provided)

Instructions:
0. For content detect original email language ie English, Russian, Estonian, German or another, use detected language for creating reply
0.1 In case of Russian language use appropriate to sender gender greeting.

1. Identify Provided Information: Determine which of the above fields have been provided in the initial correspondence.
2. Identify Missing Mandatory Information: Determine which mandatory fields are missing.
2.1 Include existing information along with a missing data, like prefilled form data.

3. Compose the Email:
   - Include Provided Information: Acknowledge and confirm the information the client has already provided.
   - Including infomation use exact leyout as above fields, you need to translate it to appropriate language

   - Route:
  - From:
    - Country
    - City (if provided)
    - Postal Code (if city is not provided)
    - *Note*: For the "From" location, we need either the country and city, or the country and postal code.
  - To:
    - Country
    - City (if provided)
    - Postal Code (if city is not provided)
    - *Note*: For the "To" location, we need either the country and city, or the country and postal code.

- Loading Date or Month
- Name or Code
- Cargo
- Loading Method (ask only if there\'s a requirement mentioned)
- Transport Type (use if specified; do not ask again)
- Cargo Value (do not ask if not mentioned)
- Currency (do not ask if not mentioned)
- Pallets: (ask quantity if pallets are mentioned)
- LDM (ask if pallets are not mentioned)

- Weight

- Dimensions:
  - Length: mandatory
  - Width: mandatory
  - Height: mandatory
  - *OR*
  - Volume (if dimensions are not provided)

   - Politely Request Missing Information: Ask for the missing mandatory information in a clear and professional manner.
     - For both FROM and TO locations, ensure you request either the country and city, or the country and postal code, if not already provided.
   - Optional Fields: If optional fields are typically required for processing but were not provided, consider if it\'s appropriate to request them.
   - Language: Use formal and polite language appropriate for professional correspondence.
   - Do Not Ask For:
     - Cargo Value and Currency if they were not mentioned.
     - Transport Type if it\'s already specified.

    Conciseness Requirement:
        - Be brief and direct. Use simple, concise language.
        - Avoid unnecessary phrases or formalities.
        - Request only the essential missing information without added explanations.

4. Formatting: Ensure the email is well-organized and easy to read.

5. generated emails examples

### Example 1 

<p>Приветствую, Иван</p>
<p>Спасибо за предоставленную информацию. Для продолжения, пожалуйста, предоставьте недостающие данные:</p>

<p>Маршрут:</p>

<p>Откуда:</p>
<ul>
  <li>Страна: США</li>
  <li>Город: Майами</li>
  <li>Индекс:</li>
</ul>
<p>* Примечание: Для локации "Откуда" нам нужно либо страна и город, либо страна и почтовый индекс.</p>

<p>Куда:</p>
<ul>
  <li>Страна: ОАЭ</li>
  <li>Город: Дубай</li>
  <li>Почтовый индекс:</li>
</ul>
<p>* Примечание: Для локации "Куда" нам нужно либо страна и город, либо страна и почтовый индекс.</p>

<p><b>Дата или месяц загрузки:</b></p>

<p>Наименование груза или HS/ТНВЭД код: 0901.21</p>

<p>Способ загрузки:</p>
<p>(если требуется, пожалуйста, уточните)</p>

<p>Тип транспорта: MSC</p>

<p><b>Поддоны (количество):</b></p>
<p>ИЛИ</p>
<p><b>LDM:</b></p>

<p><b>Вес:</b></p>

<p><b>Габариты:</b></p>
<ul>
  <li><b>Длина:</b></li>
  <li><b>Ширина:</b></li>
  <li><b>Высота:</b></li>
</ul>
<p>ИЛИ</p>
<p><b>Объем:</b></p>

### END OF EXAMPLE_1


### Example_2
Dear, Ivan

<p>Thank you for the provided information. To continue, please provide the missing information:</p>
<p>From:</p>
<ul>
  <li>Country: USA</li>
  <li>City:</li>
  <li>Postal Code: 10022</li>
</ul>
<p>Note: For the "From" location, we need either the country and city, or the country and postal code.</p>

<p>To:</p>
<ul>
  <li>Country: UAE</li>
  <li>City: Dubai</li>
  <li>Postal Code:</li>
</ul>
<p>Note: For the "To" location, we need either the country and city, or the country and postal code.</p>

<p><b>Loading Date or Month:</b></p>

<p>Cargo name or HS/ТНВЭД Code: Passenger cars, yacht in dismantled form</p>

<p>Loading Method: (if required, please specify)</p>

<p>Transport Type: MSC</p>

<p><b>Pallets (quantity):</b></p>
<p>OR</p>
<p><b>LDM:</b></p>

<p><b>Weight:</b></p>

<p><b>Dimensions:</b></p>
<ul>
  <li><b>Length:</b></li>
  <li><b>Width:</b></li>
  <li><b>Height:</b></li>
</ul>
<p>OR</p>
<p><b>Volume:</b></p>


### END OF EXAMPLE_2

Output:
Formatting: Ensure the email is well-organized and easy to read.
Use HTML formatting to structure the email.
Use <b> tags to highlight missing mandatory information.
Compose the email following the above instructions.


Output:

Compose the email following the above instructions, using HTML formatting and bolding missing mandatory information with <b> tags.
DO NOT ADD markup LIKE ``` HTML or any other and  formatting other than HTML. Output Clean html only, no additional formatting or comments
DO NOT ADD SIGNATURE AT ALL.

';

        $result = OpenAI::chat()->create([
            'model' => $modelName,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt
                ],
            ],
        ]);

        $usage = $result->usage ?? new \stdClass();
        $promptTokens = $usage->promptTokens ?? 0;
        $completionTokens = $usage->completionTokens ?? 0;

        // Calculate the cost
        $cost = $this->calculateApiRequestCost($modelName, $promptTokens, $completionTokens);

        // Return both the result and the cost
        return [
            'response' => $result,
            'cost' => $cost
        ];
    }

    private function calculateApiRequestCost($model, $promptTokens, $completionTokens)
    {
        $pricing = [
            'o1-mini' => ['input' => 3.00, 'output' => 12.00],
            'gpt-4o-mini' => ['input' => 0.075, 'output' => 0.300],
            'gpt-4o' => ['input' => 1.25, 'output' => 5.00],
        ];

        if (!isset($pricing[$model])) {
            throw new \Exception("Pricing for model {$model} is not defined.");
        }

        $inputCost = ($promptTokens / 1_000_000) * $pricing[$model]['input'];
        $outputCost = ($completionTokens / 1_000_000) * $pricing[$model]['output'];

        return $inputCost + $outputCost;
    }

    public function isWorthAnalyzing($contentForCategorization)
    {
        $modelName = 'gpt-4o-mini';

        $prompt = "Categorize email contents below answering with JSON output in case email can be called as price request. Format answer as true/false. Example: {\"isPriceRequest\": true}
        Do not use markdown.

        ";


        $result = OpenAI::chat()->create([
            'model' => $modelName,
            'response_format' => ['type' => 'json_object'],
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt . "\n\nEMAIL CONTENTS: {$contentForCategorization}"
                ],
            ],
        ]);

        Log::info('OpenAI API result for isWorthAnalyzing:', ['result' => var_export($result, true)]);
        $response = json_decode($result->choices[0]->message->content, true);
        Log::info('OpenAI API response for isWorthAnalyzing:', ['response' => $response]);

        // Extract token usage from the result
        $usage = $result->usage ?? new \stdClass();
        $promptTokens = $usage->promptTokens ?? 0;
        $completionTokens = $usage->completionTokens ?? 0;

        // Calculate the cost
        $cost = $this->calculateApiRequestCost($modelName, $promptTokens, $completionTokens);
        Log::info('OpenAI API cost for isWorthAnalyzing:', ['cost' => $cost]);

        // Return both the result and the cost
        return [
            'isPriceRequest' => $response['isPriceRequest'] ?? false,
            'cost' => $cost
        ];
    }
}

