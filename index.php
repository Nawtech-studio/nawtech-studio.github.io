<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Linus Multi-Doc Converter</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-10">
    <div class="max-w-3xl mx-auto bg-white p-8 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold mb-6 text-center text-blue-600">Linus Doc Converter</h1>
        
        <form id="uploadForm" enctype="multipart/form-data">
            <div class="mb-4">
                <label class="block font-semibold mb-2">Upload Documents (PDF or Text):</label>
                <input type="file" name="docs[]" id="docs" multiple accept=".pdf,.txt" 
                       class="w-full border p-2 rounded">
            </div>

            <div class="mb-4">
                <label class="block font-semibold mb-2">Conversion Type:</label>
                <select name="action" id="action" class="w-full border p-2 rounded">
                    <option value="summary">Generate Summary</option>
                    <option value="csv">Extract Data to CSV Table</option>
                    <option value="report">Professional Report (HTML)</option>
                    <option value="excel">Extract Entities (EXCEL)</option>
                </select>
            </div>

            <button type="submit" id="submitBtn" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
                Process with LINUS AI
            </button>
        </form>

        <div id="loader" class="hidden mt-4 text-center text-blue-500 font-bold italic">
            Processing... please wait (LINUS is thinking)
        </div>

        <div id="resultArea" class="mt-8 hidden border-t pt-4">
            <h2 class="text-xl font-bold mb-2 text-gray-700">Output:</h2>
            <div id="output" class="bg-gray-50 p-4 border rounded whitespace-pre-wrap text-sm"></div>
            <button onclick="downloadResult()" class="mt-4 bg-green-600 text-white px-4 py-2 rounded">Download Output</button>
        </div>
    </div>

    <script>
        const form = document.getElementById('uploadForm');
        const outputDiv = document.getElementById('output');
        const resultArea = document.getElementById('resultArea');
        const loader = document.getElementById('loader');

        form.onsubmit = async (e) => {
            e.preventDefault();
            loader.classList.remove('hidden');
            resultArea.classList.add('hidden');

            const formData = new FormData(form);

            try {
                const response = await fetch('process.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                if(data.success) {
                    outputDiv.innerText = data.result;
                    resultArea.classList.remove('hidden');
                } else {
                    alert("Error: " + data.error);
                }
            } catch (err) {
                console.error(err);
                alert("An error occurred during processing.");
            } finally {
                loader.classList.add('hidden');
            }
        };

        function downloadResult() {
            const content = outputDiv.innerText;
            const blob = new Blob([content], { type: 'text/plain' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'gemini_converted_result.txt';
            a.click();
        }
    </script>
    <?php include 'linus_backend_code.php';?>
</body>
</html>

