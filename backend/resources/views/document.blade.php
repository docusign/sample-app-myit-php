<div class="container">
    <div class="main-title">
        Employee Equipment and Software Agreement
    </div>
    <div class="items">
        <div class="title">Selected equipment:</div>
        <ol>
            @foreach($equipments as $item)
                <li>{{ $item }}</li>
            @endforeach
        </ol>

        <div class="title">Selected software:</div>
        <ol>
            @foreach($software as $item)
                <li>{{ $item }}</li>
            @endforeach
        </ol>
    </div>
    <div class="text">
        By signing this form, I, the employee, acknowledge the equipment and software above are in
        good working order, and that I agree to the following terms:
        <br />
        <ul>
            <li>
                The equipment and software are to be used for company purposes only
            </li>
            <li>
                Upon separation from the company, I will return the equipment and software in good
                working order. If I fail to do so, I authorize the company to deduct the expenses incurred
                from my payroll.
            </li>
        </ul>
    </div>
    <div class="signature-date">
        <div class="signature">
            Employee Signature:
        </div>
        <div class="date">
            Date: {{ date('m.d.Y') }}
        </div>
    </div>
    <div class="employee-name">
        Employee Name:
    </div>
    <div class="notation">
        Documents are provided for Customer's convenience only. DocuSign makes no warranty as to the accuracy, legality or appropriateness of
        any document for any specified use. Customer is solely responsible for determining the suitability of the Documents for Customer's business
        and complying with any regulations, laws or conventions applicable to their use.
    </div>
</div>

<style>
    .container {
        font-size: 20px;
        position: relative;
        height: 1020px;
    }
    .container .main-title {
        font-size: 30px;
        margin-bottom: 20px;
    }
    .container .items ol {
        display: block;
        margin-bottom: 20px;
    }
    .container .text ul {
        list-style-type: lower-alpha;
    }
    .container .text {
        margin-bottom: 25px;
    }
    .container .signature-date {
        margin: 30px 0;
    }
    .container .signature-date .signature {
        display: inline-block;
        width: 69%;
    }
    .container .signature-date .date {
        display: inline-block;
        width: 29%;
    }
    .container .notation {
        font-size: 14px;
        position: absolute;
        bottom: 0;
    }
</style>