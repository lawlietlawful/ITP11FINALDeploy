<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $request_item->documentType->name }} - {{ $request_item->resident->full_name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Times+New+Roman&display=swap');
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white;
            }
            @page {
                size: letter;
                margin: 1in;
            }
        }
    </style>
</head>
<body class="bg-gray-100 py-10 flex justify-center no-print-bg" onload="window.print()">

    {{-- Print Controls Container --}}
    <div class="fixed top-4 right-4 no-print flex gap-3 z-50">
        <button onclick="window.print()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-sans font-medium rounded-xl shadow-lg transition-all text-sm flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Print
        </button>
        <button onclick="window.close()" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-sans font-medium rounded-xl shadow-lg transition-all text-sm">
            Close
        </button>
    </div>

    {{-- Document Printable Sheet --}}
    <div class="bg-white w-[8.5in] min-h-[11in] p-12 shadow-2xl relative box-border">
        
        {{-- Header / Official Barangay Letterhead --}}
        <div class="text-center border-b-2 border-black pb-6 mb-8">
            <p class="text-xs uppercase tracking-widest font-bold">Republic of the Philippines</p>
            <p class="text-xs uppercase tracking-widest font-bold mt-0.5">Province of Local District</p>
            <p class="text-xs uppercase tracking-widest font-bold mt-0.5">Municipality / City</p>
            <h2 class="text-xl font-extrabold uppercase mt-3 tracking-wide">Office of the Punong Barangay</h2>
        </div>

        {{-- Document Title --}}
        <div class="text-center my-10">
            <h1 class="text-3xl font-extrabold uppercase tracking-wider underline underline-offset-8">{{ $request_item->documentType->name }}</h1>
        </div>

        {{-- Salutation & Body Content --}}
        <div class="text-justify leading-relaxed text-lg space-y-6 mt-8">
            <p class="font-bold">TO WHOM IT MAY CONCERN:</p>
            
            <p class="indent-12">
                This is to certify that <span class="font-bold uppercase underline">{{ $request_item->resident->full_name }}</span>, 
                of legal age, <span class="lowercase">{{ $request_item->resident->civil_status ?? 'single/married' }}</span>, 
                and a bona fide resident of this Barangay with postal address at <span class="font-semibold">{{ $request_item->resident->address }}</span>, 
                is known to me to be of good moral character and a law-abiding citizen.
            </p>

            <p class="indent-12">
                This certification/clearance is being issued upon the request of the above-named person for the purpose of:
            </p>

            {{-- Purpose block --}}
            <div class="text-center my-6 py-3 bg-gray-50/50 border border-gray-300 mx-12">
                <p class="font-extrabold uppercase text-xl tracking-wide">{{ $request_item->purpose }}</p>
            </div>

            <p class="indent-12">
                Issued this <span class="font-bold">{{ now()->format('jS') }}</span> day of 
                <span class="font-bold">{{ now()->format('F, Y') }}</span> at the Office of the Punong Barangay.
            </p>
        </div>

        {{-- Footer Signatures Block --}}
        <div class="mt-24 pt-12 flex justify-between items-end">
            <div>
                <p class="text-xs text-gray-500 font-mono">Tracking Code: {{ $request_item->tracking_code }}</p>
                <p class="text-xs text-gray-500 font-mono mt-0.5">Fee Paid: ₱{{ number_format($request_item->documentType->fee, 2) }}</p>
            </div>
            <div class="text-center w-64">
                <div class="border-b border-black pb-1">
                    <p class="font-extrabold uppercase tracking-wide">Hon. Barangay Captain</p>
                </div>
                <p class="text-xs mt-1 uppercase tracking-wider">Punong Barangay</p>
            </div>
        </div>

        {{-- Official Seal Placeholder / Mark --}}
        <div class="absolute bottom-8 left-12 text-[10px] text-gray-400 font-sans tracking-widest">
            NOT VALID WITHOUT OFFICIAL DRY SEAL
        </div>

    </div>

</body>
</html>
