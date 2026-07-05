<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Blood - LifeSaver Blood Bank</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-red-50 via-white to-gray-100 min-h-screen flex items-center justify-center py-10">
    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-4xl border border-red-100">
        <a href="/" class="text-red-600 hover:text-red-700 font-medium transition">← Back to Home</a>
        <div class="text-center mt-4 mb-8">
            <span class="inline-block px-4 py-2 bg-red-100 text-red-700 text-sm font-semibold rounded-full mb-3">Patient Blood Request</span>
            <h2 class="text-3xl font-bold text-red-700">Request Blood Donation</h2>
            <p class="text-gray-600 mt-2">Submit accurate patient and hospital details so the blood bank team can process your request quickly.</p>
        </div>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Success!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Oops!</strong>
                <span class="block sm:inline">There were some errors with your submission.</span>
                <ul class="mt-2 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('blood.request.submit') }}" method="POST" class="space-y-2">
            @csrf

            <!-- Patient Information Section -->
            <fieldset class="border border-gray-300 p-6 rounded-md mb-8">
                <legend class="text-xl font-semibold text-gray-800 mb-4 px-2">Patient Information</legend>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700">First Name <span class="text-red-500">*</span></label>
                        <input type="text" name="first_name" id="first_name" placeholder="Enter patient first name" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-red-500 focus:border-red-500" value="{{ old('first_name') }}" required>
                    </div>
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name <span class="text-red-500">*</span></label>
                        <input type="text" name="last_name" id="last_name" placeholder="Enter patient last name" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-red-500 focus:border-red-500" value="{{ old('last_name') }}" required>
                    </div>
                    <div>
                        <label for="mobile_number" class="block text-sm font-medium text-gray-700">Mobile Number</label>
                        <input type="text" name="mobile_number" id="mobile_number" placeholder="Example: 3065551234" pattern="[0-9+()\-\s]{7,20}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-red-500 focus:border-red-500" value="{{ old('mobile_number') }}">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" id="email" placeholder="patient@example.com" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-red-500 focus:border-red-500" value="{{ old('email') }}" required>
                    </div>
                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700">Gender <span class="text-red-500">*</span></label>
                        <select name="gender" id="gender" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-red-500 focus:border-red-500" required>
                            <option value="">Select Gender</option>
                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div>
                        <label for="date_of_birth" class="block text-sm font-medium text-gray-700">Date of Birth <span class="text-red-500">*</span></label>
                        <input type="date" name="date_of_birth" id="date_of_birth" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-red-500 focus:border-red-500" value="{{ old('date_of_birth') }}" required>
                    </div>
                    <div class="md:col-span-2">
                        <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                        <textarea name="address" id="address" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-red-500 focus:border-red-500">{{ old('address') }}</textarea>
                    </div>
                    <div class="md:col-span-2">
                        <label for="hospital_name" class="block text-sm font-medium text-gray-700">Hospital Name <span class="text-red-500">*</span></label>
                        <input type="text" name="hospital_name" id="hospital_name" placeholder="Enter hospital or clinic name" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-red-500 focus:border-red-500" value="{{ old('hospital_name') }}" required>
                    </div>
                </div>
            </fieldset>

            <!-- Blood Request Section -->
            <fieldset class="border border-gray-300 p-6 rounded-md mb-8">
                <legend class="text-xl font-semibold text-gray-800 mb-4 px-2">Blood Request Details</legend>
                <p class="text-sm text-gray-500 mb-4">Emergency requests should include the required date and any important notes for faster review.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="blood_group_id" class="block text-sm font-medium text-gray-700">Blood Group Needed <span class="text-red-500">*</span></label>
                        <select name="blood_group_id" id="blood_group_id" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-red-500 focus:border-red-500" required>
                            <option value="">Select Blood Group</option>
                            @foreach ($bloodGroups as $bloodGroup)
                                <option value="{{ $bloodGroup->id }}" {{ old('blood_group_id') == $bloodGroup->id ? 'selected' : '' }}>
                                    {{ $bloodGroup->group_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="units_requested" class="block text-sm font-medium text-gray-700">Units Requested (1 unit of blood is typically around 450–500 ml ) <span class="text-red-500">*</span></label>
                        <input type="number" name="units_requested" id="units_requested" min="1" max="10" placeholder="Example: 2" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-red-500 focus:border-red-500" value="{{ old('units_requested') }}" required>
                    </div>
                    <div>
                        <label for="urgency_level" class="block text-sm font-medium text-gray-700">Urgency Level <span class="text-red-500">*</span></label>
                        <select name="urgency_level" id="urgency_level" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-red-500 focus:border-red-500" required>
                            <option value="">Select Urgency</option>
                            <option value="routine" {{ old('urgency_level') == 'routine' ? 'selected' : '' }}>Routine</option>
                            <option value="urgent" {{ old('urgency_level') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                            <option value="emergency" {{ old('urgency_level') == 'emergency' ? 'selected' : '' }}>Emergency</option>
                        </select>
                    </div>
                    <div>
                        <label for="required_by_date" class="block text-sm font-medium text-gray-700">Required By Date</label>
                        <input type="date" name="required_by_date" id="required_by_date" min="{{ now()->toDateString() }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-red-500 focus:border-red-500" value="{{ old('required_by_date') }}">
                    </div>
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700">Additional Information</label>
                        <textarea name="description" id="description" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-red-500 focus:border-red-500">{{ old('description') }}</textarea>
                    </div>
                </div>
            </fieldset>

            <div class="bg-red-50 border border-red-100 rounded-lg p-4 text-sm text-red-800 mb-4">
                Please double-check the blood group, units requested, and hospital name before submitting. Incorrect details may delay processing.
            </div>

            <div class="flex justify-center">
                <button type="submit" class="inline-flex items-center px-8 py-3 border border-transparent text-base font-semibold rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition">
                    Submit Blood Request
                </button>
            </div>
        </form>
    </div>
</body>
</html>
