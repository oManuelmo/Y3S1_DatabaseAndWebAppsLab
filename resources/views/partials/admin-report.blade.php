<article class="report" id="report-{{ $report->reportid }}">
    <p><strong style="margin-left: -2em;">Reported Auction:</strong>
        <a href="{{ route('item.show', ['id' => $report->reportedauction]) }}" class="report-info">
            {{ $report->getItem()->name }}
        </a>
    </p>
    <p><strong style="color: black !important;">Reporter:</strong> 
        <a  href="{{ route('profile.show', ['userid' => $report->userid]) }}" class="reporter-info">
            {{ $report->getUser()->firstname ?? 'N/A' }} {{ $report->getUser()->lastname ?? 'N/A' }}
        </a>
    </p>
    <p><strong>Type:</strong> {{ $report->type }}</p>
    <p><strong>Details:</strong> 
        <span id="report-text-{{ $report->reportid }}"
            data-full-text="{{ $report->reporttext ?? 'No details provided' }}">
            {{ Str::limit($report->reporttext ?? 'No details provided', 50) }}
        </span>
    </p>
    <section class="admin-report-buttons">
        @if(strlen($report->reporttext ?? '') > 50)
            <button class="btn btn-sm btn-info" onclick="toggleText({{ $report->reportid }})">Read More</button>
        @endif
        <button onclick="deleteReport({{ $report->reportid }})" class="btn btn-danger">Delete</button>
    </section>
</article>

<div id="customModal" class="modal">
    <div class="modal-content">
        <span class="close-button" id="closeModal">&times;</span>
        <h3>Report Details</h3>
        <p id="modalReportText"></p>
    </div>
</div>

