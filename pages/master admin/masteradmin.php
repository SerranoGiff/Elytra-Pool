<?php
session_start();

// NO CACHE HEADERS
header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// VALIDATE SESSION
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'MasterAdmin') {
    header("Location: ../../index.php?error=Unauthorized access.");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Crypto Master Admin Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    /* Custom scrollbar */
    ::-webkit-scrollbar { width: 6px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background-color: #888; border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background-color: #555; }
    
    /* Hide elements with x-cloak */
    [x-cloak] { display: none !important; }
    
    /* Badge styles */
    .badge {
      padding: 0.25rem 0.5rem;
      border-radius: 0.25rem;
      font-size: 0.75rem;
      font-weight: 500;
    }
    .badge-premium { background-color: #fef9c3; color: #92400e; }
    .badge-staking { background-color: #dcfce7; color: #166534; }
    .badge-active { background-color: #dbeafe; color: #1e40af; }
    .badge-banned { background-color: #fee2e2; color: #991b1b; }
    .badge-kyc { background-color: #f3e8ff; color: #6b21a8; }
    .badge-open { background-color: #fee2e2; color: #991b1b; }
    .badge-pending { background-color: #fef9c3; color: #92400e; }
    .badge-resolved { background-color: #dcfce7; color: #166534; }
    .badge-high { background-color: #f3e8ff; color: #6b21a8; }
    .badge-normal { background-color: #dbeafe; color: #1e40af; }
    .badge-admin { background-color: #fce7f3; color: #9d174d; }
    .badge-moderator { background-color: #ecfdf5; color: #065f46; }
    
    /* Staking progress bar */
    .staking-progress {
      width: 100%;
      background-color: #e5e7eb;
      border-radius: 9999px;
      height: 0.625rem;
    }
    .staking-progress-bar {
      background-color: #2563eb;
      height: 0.625rem;
      border-radius: 9999px;
    }
  </style>
</head>

<body class="bg-gray-100 min-h-screen">
  <!-- Notification container -->
  <div id="notification-container" class="fixed top-5 right-5 space-y-2 z-50"></div>

  <!-- User Edit Modal -->
  <div id="userEditModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
      <div class="p-6">
        <h3 class="text-xl font-bold mb-4">Edit User: <span id="currentUserEmail"></span></h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">Email</label>
            <input id="userEmail" type="email" class="mt-1 block w-full border rounded px-3 py-2">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Username</label>
            <input id="userUsername" type="text" class="mt-1 block w-full border rounded px-3 py-2">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">First Name</label>
            <input id="userFirstName" type="text" class="mt-1 block w-full border rounded px-3 py-2">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Last Name</label>
            <input id="userLastName" type="text" class="mt-1 block w-full border rounded px-3 py-2">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Birthday</label>
            <input id="userBirthday" type="date" class="mt-1 block w-full border rounded px-3 py-2">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Password</label>
            <div class="flex">
              <input id="userPassword" type="password" class="mt-1 block w-full border rounded-l px-3 py-2">
              <button id="togglePassword" class="mt-1 px-3 py-2 border rounded-r bg-gray-100 hover:bg-gray-200">
                Show
              </button>
            </div>
          </div>
        </div>

        <!-- Wallet Balances -->
        <div class="border-t pt-4 mb-4">
          <h4 class="font-bold mb-2">Wallet Balances</h4>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Total Balance ($)</label>
              <input id="userBalance" type="number" step="0.01" class="mt-1 block w-full border rounded px-3 py-2">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">ELTR</label>
              <input id="userELTR" type="number" step="0.000001" class="mt-1 block w-full border rounded px-3 py-2">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">BTC</label>
              <input id="userBTC" type="number" step="0.00000001" class="mt-1 block w-full border rounded px-3 py-2">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">ETH</label>
              <input id="userETH" type="number" step="0.00000001" class="mt-1 block w-full border rounded px-3 py-2">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">USDT</label>
              <input id="userUSDT" type="number" step="0.01" class="mt-1 block w-full border rounded px-3 py-2">
            </div>
          </div>
        </div>

        <!-- Account Status -->
        <div class="border-t pt-4 mb-4">
          <h4 class="font-bold mb-2">Account Status</h4>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="flex items-center">
              <input id="userActive" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
              <label for="userActive" class="ml-2 block text-sm text-gray-700">Active Account</label>
            </div>
            <div class="flex items-center">
              <input id="userBanned" type="checkbox" class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
              <label for="userBanned" class="ml-2 block text-sm text-gray-700">Banned</label>
            </div>
          </div>
        </div>

        <!-- KYC Status -->
        <div class="border-t pt-4 mb-4">
          <h4 class="font-bold mb-2">KYC Verification</h4>
          <div class="flex items-center space-x-4">
            <span id="kycStatusText" class="text-sm"></span>
            <div id="verifyKYCButtonContainer">
              <button id="verifyKYCButton" class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600 text-xs">
                Verify KYC
              </button>
            </div>
            <button id="viewKYCDocsButton" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 text-xs">
              View KYC Docs
            </button>
          </div>
        </div>

        <!-- Premium Status -->
        <div class="border-t pt-4 mb-4">
          <h4 class="font-bold mb-2">Premium Status</h4>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="flex items-center">
              <input id="userPremium" type="checkbox" class="h-4 w-4 text-yellow-600 focus:ring-yellow-500 border-gray-300 rounded">
              <label for="userPremium" class="ml-2 block text-sm text-gray-700">Premium User</label>
            </div>
            <div id="premiumExpiryContainer" class="hidden">
              <label class="block text-sm font-medium text-gray-700">Premium Expiry</label>
              <input id="userPremiumExpiry" type="date" class="mt-1 block w-full border rounded px-3 py-2">
            </div>
          </div>
        </div>

        <!-- Admin Access Section (Only visible to master admin) -->
        <div id="adminAccessSection" class="border-t pt-4 mb-4 hidden">
          <h4 class="font-bold mb-2">Admin Access</h4>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="flex items-center">
              <input id="userIsAdmin" type="checkbox" class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
              <label for="userIsAdmin" class="ml-2 block text-sm text-gray-700">Make Admin</label>
            </div>
            <div class="flex items-center">
              <input id="userIsModerator" type="checkbox" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
              <label for="userIsModerator" class="ml-2 block text-sm text-gray-700">Make Moderator</label>
            </div>
            <div id="moderatorAccessContainer" class="hidden">
              <label class="block text-sm font-medium text-gray-700">Grant Access To Users</label>
              <select id="moderatorAccessUsers" multiple class="mt-1 block w-full border rounded px-3 py-2">
                <!-- Options will be populated dynamically -->
              </select>
              <p class="text-xs text-gray-500 mt-1">Hold Ctrl/Cmd to select multiple users</p>
            </div>
          </div>
        </div>

        <!-- Staking Positions -->
        <div class="border-t pt-4 mb-4">
          <h4 class="font-bold mb-2">Staking Positions</h4>
          <div id="stakingPositionsContainer" class="space-y-4">
            <!-- Staking positions will be added here dynamically -->
          </div>
          
          <button id="addStakingButton" class="w-full bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 flex items-center justify-center mt-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            Add Staking Position
          </button>
        </div>

        <div class="flex justify-end space-x-3 pt-4 border-t">
          <button id="cancelUserEdit" class="px-4 py-2 border rounded hover:bg-gray-100">
            Cancel
          </button>
          <button id="saveUserChanges" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Save Changes
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Staking Edit Modal -->
  <div id="stakingEditModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
      <div class="p-6">
        <h3 id="stakingModalTitle" class="text-xl font-bold mb-4">Add Staking</h3>
        
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">Coin</label>
            <select id="stakingCoin" class="mt-1 block w-full border rounded px-3 py-2">
              <option value="ELTR">Elytra (ELTR)</option>
              <option value="BTC">Bitcoin (BTC)</option>
              <option value="ETH">Ethereum (ETH)</option>
              <option value="USDT">Tether (USDT)</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Amount</label>
            <input id="stakingAmount" type="number" step="0.000001" class="mt-1 block w-full border rounded px-3 py-2">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">APY (%)</label>
            <input id="stakingAPY" type="number" step="0.1" class="mt-1 block w-full border rounded px-3 py-2">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Start Date</label>
            <input id="stakingStartDate" type="date" class="mt-1 block w-full border rounded px-3 py-2">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">End Date</label>
            <input id="stakingEndDate" type="date" class="mt-1 block w-full border rounded px-3 py-2">
          </div>
        </div>

        <div class="flex justify-end space-x-3 pt-4 mt-4 border-t">
          <button id="cancelStakingEdit" class="px-4 py-2 border rounded hover:bg-gray-100">
            Cancel
          </button>
          <button id="saveStaking" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Save
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- KYC Document Modal -->
  <div id="kycDocumentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
      <div class="p-6">
        <h3 class="text-xl font-bold mb-4">KYC Document Verification</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
          <div>
            <h4 class="font-bold mb-2">User Information</h4>
            <div class="space-y-2">
              <p><strong>Name:</strong> <span id="kycName"></span></p>
              <p><strong>Email:</strong> <span id="kycEmail"></span></p>
              <p><strong>Username:</strong> <span id="kycUsername"></span></p>
              <p><strong>ID Type:</strong> <span id="kycIdType"></span></p>
              <p><strong>ID Number:</strong> <span id="kycIdNumber"></span></p>
              <p><strong>Submitted:</strong> <span id="kycSubmittedAt"></span></p>
            </div>
          </div>
          
          <div>
            <h4 class="font-bold mb-2">Documents</h4>
            <div class="grid grid-cols-1 gap-4">
              <div>
                <p class="text-sm font-medium mb-1">ID Document (Front)</p>
                <img id="kycDocFront" src="" alt="ID Front" class="w-full rounded border cursor-pointer hover:shadow-md">
              </div>
              <div>
                <p class="text-sm font-medium mb-1">ID Document (Back)</p>
                <img id="kycDocBack" src="" alt="ID Back" class="w-full rounded border cursor-pointer hover:shadow-md">
              </div>
              <div id="kycSelfieContainer" class="hidden">
                <p class="text-sm font-medium mb-1">Selfie with ID</p>
                <img id="kycSelfie" src="" alt="Selfie" class="w-full rounded border cursor-pointer hover:shadow-md">
              </div>
            </div>
          </div>
        </div>

        <div class="border-t pt-4 mb-4">
          <h4 class="font-bold mb-2">Verification Status</h4>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div class="flex items-center">
              <input type="radio" id="kyc-pending" name="kycStatus" value="pending" class="h-4 w-4 text-yellow-600 focus:ring-yellow-500 border-gray-300">
              <label for="kyc-pending" class="ml-2 block text-sm text-gray-700">Pending Review</label>
            </div>
            <div class="flex items-center">
              <input type="radio" id="kyc-verified" name="kycStatus" value="verified" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300">
              <label for="kyc-verified" class="ml-2 block text-sm text-gray-700">Verified</label>
            </div>
            <div class="flex items-center">
              <input type="radio" id="kyc-rejected" name="kycStatus" value="rejected" class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300">
              <label for="kyc-rejected" class="ml-2 block text-sm text-gray-700">Rejected</label>
            </div>
          </div>
          
          <div id="rejectionReasonContainer" class="hidden">
            <label class="block text-sm font-medium text-gray-700 mb-1">Rejection Reason</label>
            <textarea id="rejectionReason" rows="3" class="w-full border rounded p-2" placeholder="Specify reason for rejection..."></textarea>
          </div>
        </div>

        <div class="flex justify-end space-x-3 pt-4 border-t">
          <button id="cancelKYCEdit" class="px-4 py-2 border rounded hover:bg-gray-100">
            Cancel
          </button>
          <button id="saveKYCStatus" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Save Changes
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Image Preview Modal -->
  <div id="imagePreviewModal" class="fixed inset-0 bg-black bg-opacity-75 hidden items-center justify-center z-50">
    <div class="max-w-4xl max-h-[90vh] p-4">
      <img id="currentImagePreview" src="" alt="Document Preview" class="max-w-full max-h-full">
    </div>
  </div>

  <!-- Reply Modal -->
  <div id="ticketReplyModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
      <div class="p-6">
        <h3 class="text-xl font-bold mb-4">Reply to Ticket</h3>
        <p class="text-sm text-gray-600 mb-1">From: <span id="ticketEmail"></span></p>
        <p class="text-sm text-gray-500 mb-4">Subject: <span id="ticketSubject"></span></p>
        <textarea id="replyMessage" rows="4" class="w-full border rounded p-2 mb-4" placeholder="Type your reply..."></textarea>
        <div class="flex justify-end space-x-2">
          <button id="cancelTicketReply" class="bg-gray-500 text-white px-3 py-1 rounded hover:bg-gray-600">Cancel</button>
          <button id="sendTicketReply" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">Send</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Activity Logs Modal -->
  <div id="activityLogsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] flex flex-col">
      <div class="p-4 border-b flex justify-between items-center">
        <h3 class="text-xl font-bold">Activity Logs</h3>
        <button id="closeActivityLogs" class="text-gray-500 hover:text-gray-700">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
      
      <!-- Filters -->
      <div class="p-4 border-b bg-gray-50">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Log Type</label>
            <select id="logFilterType" class="w-full p-2 border rounded">
              <option value="all">All Types</option>
              <option value="admin_action">Admin Actions</option>
              <option value="staking">Staking</option>
              <option value="kyc">KYC</option>
              <option value="support">Support</option>
              <option value="system">System</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Date Range</label>
            <select id="logFilterDate" class="w-full p-2 border rounded">
              <option value="all">All Time</option>
              <option value="today">Today</option>
              <option value="week">This Week</option>
              <option value="month">This Month</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
            <input type="text" placeholder="Search logs..." id="logSearchQuery" class="w-full p-2 border rounded">
          </div>
        </div>
      </div>
      
      <!-- Logs Table -->
      <div class="flex-1 overflow-y-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50 sticky top-0">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Message</th>
            </tr>
          </thead>
          <tbody id="logsTableBody" class="bg-white divide-y divide-gray-200">
            <!-- Logs will be added here dynamically -->
          </tbody>
        </table>
      </div>
      
      <!-- Pagination -->
      <div class="p-4 border-t flex justify-between items-center">
        <div id="logsPaginationInfo" class="text-sm text-gray-500"></div>
        <div class="flex gap-2">
          <button id="prevLogPage" class="px-3 py-1 border rounded text-xs hover:bg-gray-100">
            Previous
          </button>
          <button id="nextLogPage" class="px-3 py-1 border rounded text-xs hover:bg-gray-100">
            Next
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Log Detail Modal -->
  <div id="logDetailModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl">
      <div class="p-4 border-b flex justify-between items-center">
        <h3 class="text-xl font-bold">Log Details</h3>
        <button id="closeLogDetail" class="text-gray-500 hover:text-gray-700">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
      <div id="logDetailContent" class="p-6">
        <!-- Log details will be added here dynamically -->
      </div>
      <div class="p-4 border-t flex justify-end">
        <button id="closeLogDetailButton" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
          Close
        </button>
      </div>
    </div>
  </div>

  <!-- Navbar -->
  <nav class="bg-white text-black px-4 py-4 flex items-center justify-between shadow-sm">
    <div class="flex items-center space-x-4">
      <h1 class="text-lg lg:text-xl font-bold">Crypto Master Admin</h1>
    </div>
    <div class="flex items-center space-x-4">
      <!-- KYC Verifications Link -->
      <a href="pages/master admin/kyc-verifications.html" class="flex items-center bg-blue-600 px-3 py-1 rounded hover:bg-blue-700 space-x-2">
        <span>KYC Verifications</span>
      </a>
      
      <!-- Customer Support Link -->
      <a href="pages/master admin/customer-service.html" class="flex items-center bg-blue-600 px-3 py-1 rounded hover:bg-blue-700 space-x-2">
        <span>Customer Support</span>
      </a>

       <!-- Support Tools Support Link -->
      <a href="pages/master admin/support.html" class="flex items-center bg-blue-600 px-3 py-1 rounded hover:bg-blue-700 space-x-2">
        <span>Support Tools</span>
      </a>
      
      <!-- Logout Button -->
      <a href="../../config/logout.php" class="flex items-center bg-red-600 px-3 py-1 rounded hover:bg-red-700 space-x-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd" />
        </svg>
        <span>Logout</span>
      </a>
    </div>
  </nav>

  <!-- Main Content -->
  <main class="flex-1 p-4 lg:p-6">
    <!-- Dashboard Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
      <div class="bg-blue-50 p-4 rounded border border-blue-100">
        <h3 class="font-bold text-blue-800">Total Users</h3>
        <p id="totalUsersCount" class="text-2xl">0</p>
      </div>
      <div class="bg-green-50 p-4 rounded border border-green-100">
        <h3 class="font-bold text-green-800">Active Staking</h3>
        <p id="activeStakingCount" class="text-2xl">0</p>
      </div>
      <div class="bg-yellow-50 p-4 rounded border border-yellow-100">
        <h3 class="font-bold text-yellow-800">Premium Users</h3>
        <p id="premiumUsersCount" class="text-2xl">0</p>
      </div>
      <div class="bg-purple-50 p-4 rounded border border-purple-100">
        <h3 class="font-bold text-purple-800">Pending KYC</h3>
        <p id="pendingKYCCount" class="text-2xl">0</p>
      </div>
    </div>

    <h2 class="text-2xl font-bold mb-4">Manage Accounts</h2>

    <!-- Search and Filters -->
    <div class="flex flex-col md:flex-row gap-4 mb-4">
      <input type="text" placeholder="Search users..." id="searchQuery" class="p-2 border rounded flex-1">
      <select id="filterType" class="p-2 border rounded">
        <option value="all">All Types</option>
        <option value="standard">Standard</option>
        <option value="premium">Premium</option>
        <option value="admin">Admin</option>
        <option value="moderator">Moderator</option>
      </select>
      <select id="filterStatus" class="p-2 border rounded">
        <option value="all">All Status</option>
        <option value="active">Active</option>
        <option value="banned">Banned</option>
        <option value="kyc_pending">KYC Pending</option>
      </select>
    </div>

    <!-- User Management Table -->
    <div class="bg-white shadow rounded p-4 mb-6 overflow-x-auto">
      <table class="min-w-full">
        <thead>
          <tr class="bg-gray-100">
            <th class="p-2 text-left">Email</th>
            <th class="p-2 text-left hidden md:table-cell">Name</th>
            <th class="p-2 text-left">Balance</th>
            <th class="p-2 text-left hidden sm:table-cell">Status</th>
            <th class="p-2 text-left hidden lg:table-cell">KYC</th>
            <th class="p-2 text-left hidden lg:table-cell">Staking</th>
            <th class="p-2 text-left">Actions</th>
          </tr>
        </thead>
        <tbody id="usersTableBody">
          <!-- Users will be added here dynamically -->
        </tbody>
      </table>
      <div class="flex justify-between items-center mt-4">
        <div id="paginationInfo" class="text-sm text-gray-500"></div>
        <div class="flex gap-2">
          <button id="prevPage" class="px-3 py-1 border rounded text-xs hover:bg-gray-100">
            Previous
          </button>
          <button id="nextPage" class="px-3 py-1 border rounded text-xs hover:bg-gray-100">
            Next
          </button>
        </div>
      </div>
    </div>

    <!-- Recent Activity Logs -->
    <div class="bg-white shadow rounded p-4">
      <div class="flex justify-between items-center">
        <h3 class="text-xl font-semibold mb-2">Recent Activity Logs</h3>
        <a href="#" id="viewAllLogs" class="text-blue-600 hover:underline">View All</a>
      </div>
      <div id="recentLogsContainer" class="space-y-2 max-h-60 overflow-y-auto text-sm text-gray-700 pr-2">
        <!-- Recent logs will be added here dynamically -->
      </div>
    </div>
  </main>

  <script>
    // App State
    const appState = {
      currentUser: null,
      currentPage: 1,
      itemsPerPage: 10,
      searchQuery: '',
      filterType: 'all',
      filterStatus: 'all',
      editingStakingIndex: null,
      currentStaking: {
        coin: 'ELTR',
        amount: 0,
        apy: 5,
        startDate: '',
        endDate: ''
      },
      currentKYC: null,
      currentImage: '',
      kycStatus: 'pending',
      rejectionReason: '',
      currentTicket: null,
      replyMessage: '',
      logFilterType: 'all',
      logFilterDate: 'all',
      logSearchQuery: '',
      logCurrentPage: 1,
      logItemsPerPage: 10,
      currentLog: {},
      showPassword: false,
      
      // Current logged in admin (simulating session)
      currentAdmin: {
        id: 1,
        email: "master@crypto.com",
        isMasterAdmin: true,
        isModerator: false,
        managedUsers: [] // For moderators, this would contain user IDs they can manage
      },
      
      // Sample Data
      users: [
        {
          id: 1,
          email: "user1@crypto.com",
          username: "user1",
          password: "pass123",
          firstName: "John",
          lastName: "Doe",
          birthday: "1990-05-15",
          balance: 1500.50,
          balances: {
            eltr: 5000,
            btc: 0.215,
            eth: 1.75,
            usdt: 3400
          },
          kycVerified: true,
          kycRequested: true,
          active: true,
          isBanned: false,
          isPremium: false,
          isAdmin: false,
          isModerator: false,
          premiumExpiry: "",
          stakingPositions: [
            { 
              coin: "ELTR", 
              amount: 1000, 
              apy: 5.2, 
              startDate: "2023-11-01", 
              endDate: "2023-12-01",
              progress: 50
            },
            { 
              coin: "BTC", 
              amount: 0.1, 
              apy: 6.5, 
              startDate: "2023-11-10", 
              endDate: "2023-12-10",
              progress: 30
            }
          ],
          lastActive: "2023-11-20 09:30:22",
          managedBy: [] // For users managed by moderators
        },
        {
          id: 2,
          email: "premium_user@crypto.com",
          username: "premium1",
          password: "premiumpass",
          firstName: "Jane",
          lastName: "Smith",
          birthday: "1985-08-22",
          balance: 8500.00,
          balances: {
            eltr: 12000,
            btc: 0.5,
            eth: 3.2,
            usdt: 5000
          },
          kycVerified: true,
          kycRequested: true,
          active: true,
          isBanned: false,
          isPremium: true,
          isAdmin: false,
          isModerator: false,
          premiumExpiry: "2024-05-20",
          stakingPositions: [
            { 
              coin: "ELTR", 
              amount: 5000, 
              apy: 6.0, 
              startDate: "2023-11-10", 
              endDate: "2024-02-10",
              progress: 25
            },
            { 
              coin: "ETH", 
              amount: 2.0, 
              apy: 5.5, 
              startDate: "2023-11-15", 
              endDate: "2024-05-15",
              progress: 10
            }
          ],
          lastActive: "2023-11-20 10:05:45",
          managedBy: []
        },
        {
          id: 3,
          email: "admin1@crypto.com",
          username: "admin1",
          password: "adminpass",
          firstName: "Admin",
          lastName: "User",
          birthday: "1980-01-10",
          balance: 10000.00,
          balances: {
            eltr: 20000,
            btc: 1.0,
            eth: 5.0,
            usdt: 10000
          },
          kycVerified: true,
          kycRequested: true,
          active: true,
          isBanned: false,
          isPremium: true,
          isAdmin: true,
          isModerator: false,
          premiumExpiry: "2025-01-01",
          stakingPositions: [],
          lastActive: "2023-11-20 08:45:30",
          managedBy: []
        },
        {
          id: 4,
          email: "moderator1@crypto.com",
          username: "moderator1",
          password: "modpass",
          firstName: "Moderator",
          lastName: "User",
          birthday: "1985-06-15",
          balance: 5000.00,
          balances: {
            eltr: 10000,
            btc: 0.5,
            eth: 2.5,
            usdt: 5000
          },
          kycVerified: true,
          kycRequested: true,
          active: true,
          isBanned: false,
          isPremium: true,
          isAdmin: false,
          isModerator: true,
          premiumExpiry: "2024-06-15",
          stakingPositions: [],
          lastActive: "2023-11-20 09:30:00",
          managedBy: [],
          managedUsers: [1, 2] // This moderator can manage user1 and premium_user
        },
        {
          id: 5,
          email: "banned_user@crypto.com",
          username: "banned1",
          password: "bannedpass",
          firstName: "Robert",
          lastName: "Johnson",
          birthday: "1978-11-03",
          balance: 320.75,
          balances: {
            eltr: 500,
            btc: 0.05,
            eth: 0.2,
            usdt: 200
          },
          kycVerified: false,
          kycRequested: true,
          active: false,
          isBanned: true,
          isPremium: false,
          isAdmin: false,
          isModerator: false,
          premiumExpiry: "",
          stakingPositions: [],
          lastActive: "2023-11-15 14:30:10",
          managedBy: []
        },
        {
          id: 6,
          email: "kyc_pending@crypto.com",
          username: "kycuser",
          password: "kycpass",
          firstName: "Alice",
          lastName: "Williams",
          birthday: "1992-04-18",
          balance: 1200.00,
          balances: {
            eltr: 3000,
            btc: 0.1,
            eth: 0.5,
            usdt: 1000
          },
          kycVerified: false,
          kycRequested: true,
          active: true,
          isBanned: false,
          isPremium: false,
          isAdmin: false,
          isModerator: false,
          premiumExpiry: "",
          stakingPositions: [
            { 
              coin: "USDT", 
              amount: 500, 
              apy: 3.5, 
              startDate: "2023-11-18", 
              endDate: "2024-02-18",
              progress: 15
            }
          ],
          lastActive: "2023-11-19 11:20:15",
          managedBy: [4] // Managed by moderator1
        }
      ],
      logs: [
        { 
          id: 1,
          time: "2023-11-20 09:30:22", 
          message: "Premium user staked 5000 ELTR at 6.0% APY",
          type: "staking",
          user: "premium_user@crypto.com",
          details: {
            amount: 5000,
            coin: "ELTR",
            apy: 6.0,
            duration: "90 days"
          }
        },
        { 
          id: 2,
          time: "2023-11-20 09:15:10", 
          message: "Admin approved withdrawal of 0.5 BTC",
          type: "admin_action",
          user: "admin1@crypto.com",
          details: {
            amount: 0.5,
            coin: "BTC",
            wallet: "3FZbgi29cpjq2GjdwV8eyHuJJnkLtktZc5",
            status: "completed"
          }
        },
        { 
          id: 3,
          time: "2023-11-20 08:45:30", 
          message: "KYC verified for user1@crypto.com",
          type: "kyc",
          user: "admin1@crypto.com",
          details: {
            name: "John Doe",
            idType: "Passport",
            idNumber: "P123456789"
          }
        },
        { 
          id: 4,
          time: "2023-11-19 15:20:00", 
          message: "User banned_user@crypto.com was banned for violation of terms",
          type: "admin_action",
          user: "admin1@crypto.com",
          details: {
            reason: "Multiple failed login attempts",
            action: "Account suspended",
            duration: "Permanent"
          }
        },
        { 
          id: 5,
          time: "2023-11-18 14:10:45", 
          message: "User kyc_pending@crypto.com submitted KYC documents",
          type: "kyc",
          user: "kyc_pending@crypto.com",
          details: {
            idType: "Driver's License",
            status: "pending_review"
          }
        },
        { 
          id: 6,
          time: "2023-11-18 10:30:15", 
          message: "System maintenance completed",
          type: "system",
          user: "system",
          details: {
            duration: "2 hours",
            components: ["Database", "API", "Frontend"],
            status: "success"
          }
        },
        { 
          id: 7,
          time: "2023-11-17 16:45:22", 
          message: "Support ticket #1002 resolved",
          type: "support",
          user: "admin1@crypto.com",
          details: {
            ticketId: 1002,
            subject: "KYC verification stuck",
            resolution: "Documents verified"
          }
        },
        { 
          id: 8,
          time: "2023-11-16 11:20:33", 
          message: "User premium_user@crypto.com upgraded to Premium",
          type: "admin_action",
          user: "admin1@crypto.com",
          details: {
            plan: "Premium Annual",
            expiry: "2024-11-16"
          }
        },
        { 
          id: 9,
          time: "2023-11-15 09:10:05", 
          message: "New user registered: trader_john@crypto.com",
          type: "system",
          user: "system",
          details: {
            referral: "google_ads",
            verification: "pending"
          }
        },
        { 
          id: 10,
          time: "2023-11-14 14:30:18", 
          message: "Security alert: Unusual login attempt",
          type: "system",
          user: "system",
          details: {
            ip: "192.168.1.100",
            location: "Unknown",
            action: "Blocked"
          }
        },
        { 
          id: 11,
          time: "2023-11-13 12:15:45", 
          message: "Moderator updated balance for user1@crypto.com",
          type: "admin_action",
          user: "moderator1@crypto.com",
          details: {
            action: "Balance update",
            amount: "+500.00 USD",
            reason: "Bonus credit"
          }
        }
      ],
      kycRequests: [
        {
          id: 1,
          email: "user1@crypto.com",
          username: "user1",
          name: "Juan Dela Cruz",
          idType: "Passport",
          idNumber: "P123456789",
          documentFront: "https://via.placeholder.com/300x180?text=Passport+Front",
          documentBack: "https://via.placeholder.com/300x180?text=Passport+Back",
          selfie: "https://via.placeholder.com/300x180?text=Selfie+with+ID",
          status: "pending",
          submittedAt: "2023-11-18T09:30:22",
          updatedAt: "2023-11-18T09:30:22",
          unread: true
        },
        {
          id: 2,
          email: "user2@crypto.com",
          username: "user2",
          name: "Maria Santos",
          idType: "Driver's License",
          idNumber: "DL987654321",
          documentFront: "https://via.placeholder.com/300x180?text=License+Front",
          documentBack: "https://via.placeholder.com/300x180?text=License+Back",
          selfie: "https://via.placeholder.com/300x180?text=Selfie+with+License",
          status: "verified",
          submittedAt: "2023-11-15T11:45:33",
          updatedAt: "2023-11-16T15:30:00",
          unread: false
        },
        {
          id: 3,
          email: "user3@crypto.com",
          username: "user3",
          name: "Pedro Reyes",
          idType: "National ID",
          idNumber: "NID11223344",
          documentFront: "https://via.placeholder.com/300x180?text=National+ID+Front",
          documentBack: "https://via.placeholder.com/300x180?text=National+ID+Back",
          selfie: null,
          status: "rejected",
          submittedAt: "2023-11-10T08:15:45",
          updatedAt: "2023-11-12T10:20:15",
          rejectionReason: "ID document expired",
          unread: false
        },
        {
          id: 4,
          email: "user4@crypto.com",
          username: "user4",
          name: "Anna Lopez",
          idType: "Passport",
          idNumber: "P987654321",
          documentFront: "https://via.placeholder.com/300x180?text=Passport+Front",
          documentBack: "https://via.placeholder.com/300x180?text=Passport+Back",
          selfie: "https://via.placeholder.com/300x180?text=Selfie+with+Passport",
          status: "pending",
          submittedAt: "2023-11-20T14:30:10",
          updatedAt: "2023-11-20T14:30:10",
          unread: true
        },
        {
          id: 5,
          email: "user5@crypto.com",
          username: "user5",
          name: "Carlos Gomez",
          idType: "Voter's ID",
          idNumber: "V12345678",
          documentFront: "https://via.placeholder.com/300x180?text=Voter's+ID+Front",
          documentBack: "https://via.placeholder.com/300x180?text=Voter's+ID+Back",
          selfie: null,
          status: "pending",
          submittedAt: "2023-11-19T16:20:30",
          updatedAt: "2023-11-19T16:20:30",
          unread: false
        }
      ],
      supportTickets: [
        {
          id: 1001,
          email: 'user1@crypto.com',
          subject: 'Withdrawal not processing',
          message: 'I initiated a withdrawal 3 days ago but it still shows as pending. The transaction ID is TXN12345.',
          status: 'open',
          priority: 'high',
          createdAt: '2023-11-18T09:30:22',
          updatedAt: '2023-11-20T14:15:10',
          unread: true,
          replies: []
        },
        {
          id: 1002,
          email: 'premium_user@crypto.com',
          subject: 'KYC verification stuck',
          message: 'My KYC documents were submitted a week ago but still show as pending. Can you check the status?',
          status: 'pending',
          priority: 'normal',
          createdAt: '2023-11-15T11:45:33',
          updatedAt: '2023-11-19T10:20:15',
          unread: false,
          replies: [
            {
              type: 'admin',
              message: 'We have escalated your KYC verification to our compliance team. Please allow 1-2 more business days for processing.',
              date: '2023-11-16T15:30:00'
            },
            {
              type: 'user',
              message: 'Thanks for the update. I\'ll wait to hear back.',
              date: '2023-11-17T09:15:00'
            }
          ]
        },
        {
          id: 1003,
          email: 'trader_john@crypto.com',
          subject: 'Account security concern',
          message: 'I received a login notification from a new device but it wasn\'t me. Please secure my account.',
          status: 'open',
          priority: 'high',
          createdAt: '2023-11-20T08:15:45',
          updatedAt: '2023-11-20T08:15:45',
          unread: true,
          replies: []
        },
        {
          id: 1004,
          email: 'investor_sarah@crypto.com',
          subject: 'Staking rewards question',
          message: 'The staking rewards I received this month seem lower than expected based on the advertised APY. Can you explain?',
          status: 'open',
          priority: 'normal',
          createdAt: '2023-11-19T16:30:22',
          updatedAt: '2023-11-19T16:30:22',
          unread: false,
          replies: []
        },
        {
          id: 1005,
          email: 'new_user@crypto.com',
          subject: 'Verification email not received',
          message: 'I signed up but didn\'t get the verification email. I checked spam folder too. Can you resend?',
          status: 'resolved',
          priority: 'low',
          createdAt: '2023-11-14T10:20:15',
          updatedAt: '2023-11-15T09:10:30',
          unread: false,
          replies: [
            {
              type: 'admin',
              message: 'We\'ve resent the verification email. Please check your inbox and spam folder. Let us know if you still don\'t receive it.',
              date: '2023-11-14T14:45:00'
            },
            {
              type: 'user',
              message: 'Received it now, thanks! All set.',
              date: '2023-11-15T09:10:30'
            }
          ]
        }
      ]
    };

    // Helper Functions
    function formatDate(isoString) {
      if (!isoString) return '';
      const date = new Date(isoString);
      return date.toLocaleString();
    }

    function showNotification(message, type = 'success') {
      const container = document.getElementById('notification-container');
      const note = document.createElement('div');
      note.className = `bg-${type === 'success' ? 'green' : 'red'}-500 text-white px-4 py-2 rounded shadow`;
      note.textContent = message;
      container.appendChild(note);
      setTimeout(() => note.remove(), 3000);
    }

    function logActivity(message, type = 'system') {
      const user = appState.currentAdmin.email;
      appState.logs.push({
        id: Date.now(),
        time: new Date().toLocaleString(),
        message,
        type,
        user
      });
      renderRecentLogs();
    }

    // Check if current admin can manage a specific user
    function canManageUser(userId) {
      // Master admin can manage all users
      if (appState.currentAdmin.isMasterAdmin) return true;
      
      // Moderators can only manage users assigned to them
      if (appState.currentAdmin.isModerator) {
        return appState.currentAdmin.managedUsers.includes(userId);
      }
      
      return false;
    }

    // Check if current admin can perform admin-level actions
    function isMasterAdmin() {
      return appState.currentAdmin.isMasterAdmin;
    }

    // Filter Functions
    function filterUsers() {
      appState.searchQuery = document.getElementById('searchQuery').value;
      appState.filterType = document.getElementById('filterType').value;
      appState.filterStatus = document.getElementById('filterStatus').value;
      appState.currentPage = 1;
      renderUsersTable();
    }

    function filterLogs() {
      appState.logFilterType = document.getElementById('logFilterType').value;
      appState.logFilterDate = document.getElementById('logFilterDate').value;
      appState.logSearchQuery = document.getElementById('logSearchQuery').value;
      appState.logCurrentPage = 1;
      renderLogsTable();
    }

    // Render Functions
    function renderUsersTable() {
      const filteredUsers = getFilteredUsers();
      const paginatedUsers = getPaginatedUsers(filteredUsers);
      const tableBody = document.getElementById('usersTableBody');
      
      tableBody.innerHTML = '';
      
      if (filteredUsers.length === 0) {
        const row = document.createElement('tr');
        row.innerHTML = `
          <td colspan="7" class="p-4 text-center text-gray-500">No users found matching your criteria</td>
        `;
        tableBody.appendChild(row);
        return;
      }
      
      paginatedUsers.forEach(user => {
        const row = document.createElement('tr');
        row.className = 'border-b hover:bg-gray-50';
        
        // Email with mobile name
        const emailCell = document.createElement('td');
        emailCell.className = 'p-2';
        emailCell.innerHTML = `
          <div class="font-medium">${user.email}</div>
          <div class="text-xs text-gray-500 md:hidden">${user.firstName} ${user.lastName}</div>
        `;
        row.appendChild(emailCell);
        
        // Name (hidden on mobile)
        const nameCell = document.createElement('td');
        nameCell.className = 'p-2 hidden md:table-cell';
        nameCell.textContent = `${user.firstName} ${user.lastName}`;
        row.appendChild(nameCell);
        
        // Balance
        const balanceCell = document.createElement('td');
        balanceCell.className = 'p-2';
        balanceCell.textContent = `$${user.balance.toFixed(2)}`;
        row.appendChild(balanceCell);
        
        // Status (hidden on small screens)
        const statusCell = document.createElement('td');
        statusCell.className = 'p-2 hidden sm:table-cell';
        if (user.isBanned) {
          statusCell.innerHTML = '<span class="badge badge-banned">Banned</span>';
        } else if (user.active) {
          if (user.isAdmin) {
            statusCell.innerHTML = '<span class="badge badge-admin">Admin</span>';
          } else if (user.isModerator) {
            statusCell.innerHTML = '<span class="badge badge-moderator">Moderator</span>';
          } else {
            statusCell.innerHTML = '<span class="badge badge-active">Active</span>';
          }
        } else {
          statusCell.innerHTML = '<span class="text-xs text-gray-500">Inactive</span>';
        }
        row.appendChild(statusCell);
        
        // KYC (hidden on lg screens)
        const kycCell = document.createElement('td');
        kycCell.className = 'p-2 hidden lg:table-cell';
        if (user.kycVerified) {
          kycCell.innerHTML = '<span class="badge badge-kyc">Verified</span>';
        } else if (user.kycRequested) {
          kycCell.innerHTML = '<span class="text-xs text-yellow-600">Pending</span>';
        } else {
          kycCell.innerHTML = '<span class="text-xs text-gray-500">None</span>';
        }
        row.appendChild(kycCell);
        
        // Staking (hidden on lg screens)
        const stakingCell = document.createElement('td');
        stakingCell.className = 'p-2 hidden lg:table-cell';
        if (user.stakingPositions.length > 0) {
          stakingCell.innerHTML = `<span class="badge badge-staking">${user.stakingPositions.length} active</span>`;
        } else {
          stakingCell.innerHTML = '<span class="text-xs text-gray-500">None</span>';
        }
        row.appendChild(stakingCell);
        
        // Actions
        const actionsCell = document.createElement('td');
        actionsCell.className = 'p-2';
        
        // Check if current admin can manage this user
        const canManage = canManageUser(user.id);
        
        if (canManage) {
          actionsCell.innerHTML = `
            <div class="flex flex-wrap gap-1">
              <button onclick="openUserEditModal(${user.id})" class="bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600 text-xs">
                Edit
              </button>
              ${user.isAdmin || user.isModerator ? '' : `
                <button onclick="toggleBanStatus(${user.id})" class="px-2 py-1 rounded text-xs ${user.isBanned ? 'bg-green-500 hover:bg-green-600 text-white' : 'bg-red-500 hover:bg-red-600 text-white'}">
                  ${user.isBanned ? 'Unban' : 'Ban'}
                </button>
                <button onclick="togglePremiumStatus(${user.id})" class="px-2 py-1 rounded text-xs ${user.isPremium ? 'bg-gray-500 hover:bg-gray-600 text-white' : 'bg-yellow-500 hover:bg-yellow-600 text-white'}">
                  ${user.isPremium ? 'Revoke Premium' : 'Make Premium'}
                </button>
              `}
              ${isMasterAdmin() && !user.isAdmin && !user.isModerator ? `
                <button onclick="makeAdmin(${user.id})" class="bg-indigo-500 text-white px-2 py-1 rounded hover:bg-indigo-600 text-xs">
                  Make Admin
                </button>
                <button onclick="makeModerator(${user.id})" class="bg-green-500 text-white px-2 py-1 rounded hover:bg-green-600 text-xs">
                  Make Moderator
                </button>
              ` : ''}
            </div>
          `;
        } else {
          actionsCell.innerHTML = '<span class="text-xs text-gray-500">No access</span>';
        }
        
        row.appendChild(actionsCell);
        
        tableBody.appendChild(row);
      });
      
      // Update pagination info
      document.getElementById('paginationInfo').textContent = 
        `Showing ${(appState.currentPage-1)*appState.itemsPerPage+1} to ${Math.min(appState.currentPage*appState.itemsPerPage, filteredUsers.length)} of ${filteredUsers.length} users`;
      
      // Update summary counts
      document.getElementById('totalUsersCount').textContent = appState.users.length;
      document.getElementById('activeStakingCount').textContent = appState.users.reduce((count, user) => count + user.stakingPositions.length, 0);
      document.getElementById('premiumUsersCount').textContent = appState.users.filter(user => user.isPremium).length;
      document.getElementById('pendingKYCCount').textContent = appState.users.filter(user => user.kycRequested && !user.kycVerified).length;
    }

    function getFilteredUsers() {
      let result = appState.users;
      
      // For moderators, only show users they can manage
      if (appState.currentAdmin.isModerator && !appState.currentAdmin.isMasterAdmin) {
        result = result.filter(user => 
          appState.currentAdmin.managedUsers.includes(user.id) || 
          user.id === appState.currentAdmin.id
        );
      }
      
      // Apply search filter
      if (appState.searchQuery) {
        const query = appState.searchQuery.toLowerCase();
        result = result.filter(user => 
          user.email.toLowerCase().includes(query) ||
          user.username.toLowerCase().includes(query) ||
          `${user.firstName} ${user.lastName}`.toLowerCase().includes(query)
        );
      }
      
      // Apply type filter
      if (appState.filterType !== 'all') {
        if (appState.filterType === 'admin') {
          result = result.filter(user => user.isAdmin);
        } else if (appState.filterType === 'moderator') {
          result = result.filter(user => user.isModerator);
        } else {
          result = result.filter(user => 
            !user.isAdmin && !user.isModerator && 
            (appState.filterType === 'premium' ? user.isPremium : !user.isPremium)
          );
        }
      }
      
      // Apply status filter
      if (appState.filterStatus !== 'all') {
        if (appState.filterStatus === 'banned') {
          result = result.filter(user => user.isBanned);
        } else if (appState.filterStatus === 'active') {
          result = result.filter(user => !user.isBanned && user.active);
        } else if (appState.filterStatus === 'kyc_pending') {
          result = result.filter(user => user.kycRequested && !user.kycVerified);
        }
      }
      
      return result;
    }

    function getPaginatedUsers(users) {
      const start = (appState.currentPage - 1) * appState.itemsPerPage;
      return users.slice(start, start + appState.itemsPerPage);
    }

    function renderRecentLogs() {
      const container = document.getElementById('recentLogsContainer');
      container.innerHTML = '';
      
      const recentLogs = [...appState.logs].sort((a, b) => new Date(b.time) - new Date(a.time)).slice(0, 5);
      
      recentLogs.forEach(log => {
        const logElement = document.createElement('div');
        logElement.className = 'border-b py-1';
        
        const typeClass = {
          'admin_action': 'text-blue-600',
          'staking': 'text-green-600',
          'kyc': 'text-purple-600',
          'system': 'text-gray-600'
        }[log.type] || 'text-gray-600';
        
        logElement.innerHTML = `
          <div class="flex flex-col sm:flex-row sm:items-baseline">
            <span class="text-gray-500 text-xs sm:text-sm sm:mr-2">${log.time}</span>
            <span class="text-sm ${typeClass}">${log.message}</span>
          </div>
          <div class="text-xs text-gray-400 mt-1">User: ${log.user}</div>
        `;
        
        container.appendChild(logElement);
      });
    }

    function renderLogsTable() {
      const filteredLogs = getFilteredLogs();
      const paginatedLogs = getPaginatedLogs(filteredLogs);
      const tableBody = document.getElementById('logsTableBody');
      
      tableBody.innerHTML = '';
      
      if (filteredLogs.length === 0) {
        const row = document.createElement('tr');
        row.innerHTML = `
          <td colspan="4" class="px-4 py-4 text-center text-sm text-gray-500">No logs found matching your criteria</td>
        `;
        tableBody.appendChild(row);
        return;
      }
      
      paginatedLogs.forEach(log => {
        const row = document.createElement('tr');
        row.className = 'hover:bg-gray-50 cursor-pointer';
        row.onclick = () => viewLogDetails(log);
        
        const typeClass = {
          'admin_action': 'bg-blue-100 text-blue-800',
          'staking': 'bg-green-100 text-green-800',
          'kyc': 'bg-purple-100 text-purple-800',
          'support': 'bg-yellow-100 text-yellow-800',
          'system': 'bg-gray-100 text-gray-800'
        }[log.type] || 'bg-gray-100 text-gray-800';
        
        row.innerHTML = `
          <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">${log.time}</td>
          <td class="px-4 py-2 whitespace-nowrap">
            <span class="px-2 py-1 text-xs rounded-full ${typeClass}">
              ${log.type.replace('_', ' ').toUpperCase()}
            </span>
          </td>
          <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">${log.user}</td>
          <td class="px-4 py-2 text-sm text-gray-500">${log.message}</td>
        `;
        
        tableBody.appendChild(row);
      });
      
      // Update pagination info
      document.getElementById('logsPaginationInfo').textContent = 
        `Showing ${(appState.logCurrentPage-1)*appState.logItemsPerPage+1} to ${Math.min(appState.logCurrentPage*appState.logItemsPerPage, filteredLogs.length)} of ${filteredLogs.length} logs`;
    }

    function getFilteredLogs() {
      let result = [...appState.logs].sort((a, b) => new Date(b.time) - new Date(a.time));
      
      // For moderators, only show logs related to users they can manage
      if (appState.currentAdmin.isModerator && !appState.currentAdmin.isMasterAdmin) {
        result = result.filter(log => {
          // Check if log is about a user this moderator can manage
          const userMatch = appState.users.some(user => 
            appState.currentAdmin.managedUsers.includes(user.id) && 
            (log.user === user.email || log.message.includes(user.email))
          );
          
          // Also include logs created by this moderator
          return userMatch || log.user === appState.currentAdmin.email;
        });
      }
      
      // Apply type filter
      if (appState.logFilterType !== 'all') {
        result = result.filter(log => log.type === appState.logFilterType);
      }
      
      // Apply date filter
      if (appState.logFilterDate !== 'all') {
        const now = new Date();
        result = result.filter(log => {
          const logDate = new Date(log.time);
          
          if (appState.logFilterDate === 'today') {
            return logDate.toDateString() === now.toDateString();
          } else if (appState.logFilterDate === 'week') {
            const weekStart = new Date(now);
            weekStart.setDate(now.getDate() - now.getDay());
            return logDate >= weekStart;
          } else if (appState.logFilterDate === 'month') {
            return logDate.getMonth() === now.getMonth() && logDate.getFullYear() === now.getFullYear();
          }
          return true;
        });
      }
      
      // Apply search filter
      if (appState.logSearchQuery) {
        const query = appState.logSearchQuery.toLowerCase();
        result = result.filter(log => 
          log.message.toLowerCase().includes(query) ||
          log.user.toLowerCase().includes(query) ||
          log.type.toLowerCase().includes(query)
        );
      }
      
      return result;
    }

    function getPaginatedLogs(logs) {
      const start = (appState.logCurrentPage - 1) * appState.logItemsPerPage;
      return logs.slice(start, start + appState.logItemsPerPage);
    }

    function renderStakingPositions() {
      const container = document.getElementById('stakingPositionsContainer');
      container.innerHTML = '';
      
      if (!appState.currentUser || !appState.currentUser.stakingPositions) return;
      
      appState.currentUser.stakingPositions.forEach((staking, index) => {
        const stakingElement = document.createElement('div');
        stakingElement.className = 'border rounded-lg p-4 bg-gray-50';
        stakingElement.innerHTML = `
          <div class="flex justify-between items-center mb-2">
            <div class="font-medium">${staking.coin} Staking</div>
            <div class="text-sm bg-blue-100 text-blue-800 px-2 py-1 rounded">${staking.apy}% APY</div>
          </div>
          
          <div class="mb-3">
            <div class="flex justify-between text-sm mb-1">
              <span class="text-gray-500">Amount Staked</span>
              <span>${staking.amount} ${staking.coin}</span>
            </div>
            <div class="staking-progress">
              <div class="staking-progress-bar" style="width: ${staking.progress || 100}%"></div>
            </div>
          </div>
          
          <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
              <div class="text-gray-500">Start Date</div>
              <div>${staking.startDate}</div>
            </div>
            <div>
              <div class="text-gray-500">End Date</div>
              <div>${staking.endDate}</div>
            </div>
          </div>
          
          <div class="flex justify-end space-x-2 mt-3">
            <button onclick="editStaking(${index})" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 text-xs">
              Edit
            </button>
            <button onclick="removeStaking(${index})" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-xs">
              Remove
            </button>
          </div>
        `;
        
        container.appendChild(stakingElement);
      });
    }

    // Modal Functions
    function openUserEditModal(userId) {
      const user = appState.users.find(u => u.id === userId);
      if (!user) return;
      
      // Check if current admin can manage this user
      if (!canManageUser(userId)) {
        showNotification('You do not have permission to edit this user', 'error');
        return;
      }
      
      // Clone the user object to avoid direct mutation
      appState.currentUser = JSON.parse(JSON.stringify(user));
      
      // Fill the form
      document.getElementById('currentUserEmail').textContent = user.email;
      document.getElementById('userEmail').value = user.email;
      document.getElementById('userUsername').value = user.username;
      document.getElementById('userFirstName').value = user.firstName;
      document.getElementById('userLastName').value = user.lastName;
      document.getElementById('userBirthday').value = user.birthday;
      document.getElementById('userPassword').value = user.password;
      document.getElementById('userBalance').value = user.balance;
      document.getElementById('userELTR').value = user.balances.eltr;
      document.getElementById('userBTC').value = user.balances.btc;
      document.getElementById('userETH').value = user.balances.eth;
      document.getElementById('userUSDT').value = user.balances.usdt;
      document.getElementById('userActive').checked = user.active;
      document.getElementById('userBanned').checked = user.isBanned;
      document.getElementById('userPremium').checked = user.isPremium;
      document.getElementById('userPremiumExpiry').value = user.premiumExpiry;
      
      // Show/hide premium expiry
      document.getElementById('premiumExpiryContainer').classList.toggle('hidden', !user.isPremium);
      
      // KYC status
      const kycStatusText = document.getElementById('kycStatusText');
      if (user.kycVerified) {
        kycStatusText.textContent = '✅ Verified';
        kycStatusText.className = 'text-sm';
        document.getElementById('verifyKYCButtonContainer').classList.add('hidden');
      } else {
        kycStatusText.textContent = '❌ Not Verified';
        kycStatusText.className = 'text-sm';
        document.getElementById('verifyKYCButtonContainer').classList.remove('hidden');
      }
      
      // Admin access section (only for master admin)
      const adminAccessSection = document.getElementById('adminAccessSection');
      if (isMasterAdmin()) {
        adminAccessSection.classList.remove('hidden');
        document.getElementById('userIsAdmin').checked = user.isAdmin;
        document.getElementById('userIsModerator').checked = user.isModerator;
        
        // Populate moderator access users
        const moderatorAccessSelect = document.getElementById('moderatorAccessUsers');
        moderatorAccessSelect.innerHTML = '';
        
        // Only show non-admin users
        appState.users
          .filter(u => !u.isAdmin && u.id !== user.id)
          .forEach(u => {
            const option = document.createElement('option');
            option.value = u.id;
            option.textContent = `${u.email} (${u.firstName} ${u.lastName})`;
            option.selected = user.managedUsers?.includes(u.id) || false;
            moderatorAccessSelect.appendChild(option);
          });
        
        // Show/hide moderator access based on selection
        document.getElementById('moderatorAccessContainer').classList.toggle('hidden', !user.isModerator);
      } else {
        adminAccessSection.classList.add('hidden');
      }
      
      // Render staking positions
      renderStakingPositions();
      
      // Show modal
      document.getElementById('userEditModal').classList.remove('hidden');
    }

    function closeUserEditModal() {
      document.getElementById('userEditModal').classList.add('hidden');
    }

    function saveUserChanges() {
      if (!appState.currentUser) return;
      
      // Check if current admin can manage this user
      if (!canManageUser(appState.currentUser.id)) {
        showNotification('You do not have permission to edit this user', 'error');
        return;
      }
      
      // Update user data from form
      appState.currentUser.email = document.getElementById('userEmail').value;
      appState.currentUser.username = document.getElementById('userUsername').value;
      appState.currentUser.firstName = document.getElementById('userFirstName').value;
      appState.currentUser.lastName = document.getElementById('userLastName').value;
      appState.currentUser.birthday = document.getElementById('userBirthday').value;
      appState.currentUser.password = document.getElementById('userPassword').value;
      appState.currentUser.balance = parseFloat(document.getElementById('userBalance').value);
      appState.currentUser.balances.eltr = parseFloat(document.getElementById('userELTR').value);
      appState.currentUser.balances.btc = parseFloat(document.getElementById('userBTC').value);
      appState.currentUser.balances.eth = parseFloat(document.getElementById('userETH').value);
      appState.currentUser.balances.usdt = parseFloat(document.getElementById('userUSDT').value);
      appState.currentUser.active = document.getElementById('userActive').checked;
      appState.currentUser.isBanned = document.getElementById('userBanned').checked;
      appState.currentUser.isPremium = document.getElementById('userPremium').checked;
      appState.currentUser.premiumExpiry = document.getElementById('userPremiumExpiry').value;
      
      // For master admin only - update admin/moderator status
      if (isMasterAdmin()) {
        const wasAdmin = appState.currentUser.isAdmin;
        const wasModerator = appState.currentUser.isModerator;
        
        appState.currentUser.isAdmin = document.getElementById('userIsAdmin').checked;
        appState.currentUser.isModerator = document.getElementById('userIsModerator').checked;
        
        // If making someone an admin, remove moderator status
        if (appState.currentUser.isAdmin) {
          appState.currentUser.isModerator = false;
          document.getElementById('userIsModerator').checked = false;
          document.getElementById('moderatorAccessContainer').classList.add('hidden');
        }
        
        // If making someone a moderator, update their managed users
        if (appState.currentUser.isModerator) {
          const selectedOptions = Array.from(document.getElementById('moderatorAccessUsers').selectedOptions);
          appState.currentUser.managedUsers = selectedOptions.map(option => parseInt(option.value));
          
          // Update the managedBy field for all users
          appState.users.forEach(user => {
            if (selectedOptions.some(option => parseInt(option.value) === user.id)) {
              if (!user.managedBy.includes(appState.currentUser.id)) {
                user.managedBy.push(appState.currentUser.id);
              }
            } else {
              user.managedBy = user.managedBy.filter(id => id !== appState.currentUser.id);
            }
          });
        }
        
        // Log role changes
        if (wasAdmin !== appState.currentUser.isAdmin) {
          const action = appState.currentUser.isAdmin ? 'promoted to admin' : 'demoted from admin';
          logActivity(`${action} ${appState.currentUser.email}`, 'admin_action');
        }
        
        if (wasModerator !== appState.currentUser.isModerator) {
          const action = appState.currentUser.isModerator ? 'made moderator' : 'removed as moderator';
          logActivity(`${action} ${appState.currentUser.email}`, 'admin_action');
        }
      }
      
      // Find the original user and update
      const index = appState.users.findIndex(u => u.id === appState.currentUser.id);
      if (index !== -1) {
        appState.users[index] = appState.currentUser;
        logActivity(`Updated user ${appState.currentUser.email}`, 'admin_action');
        showNotification('User updated successfully');
        closeUserEditModal();
        renderUsersTable();
      }
    }

    function toggleBanStatus(userId) {
      const user = appState.users.find(u => u.id === userId);
      if (!user) return;
      
      // Check if current admin can manage this user
      if (!canManageUser(userId)) {
        showNotification('You do not have permission to modify this user', 'error');
        return;
      }
      
      user.isBanned = !user.isBanned;
      if (user.isBanned) user.active = false;
      
      const action = user.isBanned ? 'banned' : 'unbanned';
      logActivity(`${action} user ${user.email}`, 'admin_action');
      showNotification(`User ${action} successfully`);
      renderUsersTable();
    }

    function togglePremiumStatus(userId) {
      const user = appState.users.find(u => u.id === userId);
      if (!user) return;
      
      // Check if current admin can manage this user
      if (!canManageUser(userId)) {
        showNotification('You do not have permission to modify this user', 'error');
        return;
      }
      
      user.isPremium = !user.isPremium;
      if (user.isPremium) {
        const expiry = new Date();
        expiry.setFullYear(expiry.getFullYear() + 1); // 1 year premium
        user.premiumExpiry = expiry.toISOString().split('T')[0];
      } else {
        user.premiumExpiry = '';
      }
      
      const action = user.isPremium ? 'upgraded to Premium' : 'downgraded from Premium';
      logActivity(`${action} user ${user.email}`, 'admin_action');
      showNotification(`User ${action} successfully`);
      renderUsersTable();
    }

    function makeAdmin(userId) {
      if (!isMasterAdmin()) {
        showNotification('Only master admin can perform this action', 'error');
        return;
      }
      
      if (!confirm('Are you sure you want to make this user an admin?')) return;
      
      const user = appState.users.find(u => u.id === userId);
      if (!user) return;
      
      user.isAdmin = true;
      user.isModerator = false; // Can't be both
      user.managedUsers = []; // Clear any moderator assignments
      
      // Remove from any managedBy lists
      appState.users.forEach(u => {
        u.managedBy = u.managedBy.filter(id => id !== userId);
      });
      
      logActivity(`Made ${user.email} an admin`, 'admin_action');
      showNotification('User promoted to admin');
      renderUsersTable();
    }

    function makeModerator(userId) {
      if (!isMasterAdmin()) {
        showNotification('Only master admin can perform this action', 'error');
        return;
      }
      
      if (!confirm('Are you sure you want to make this user a moderator?')) return;
      
      const user = appState.users.find(u => u.id === userId);
      if (!user) return;
      
      user.isModerator = true;
      user.isAdmin = false; // Can't be both
      user.managedUsers = []; // Start with no users to manage
      
      // Open edit modal to assign users to manage
      openUserEditModal(userId);
      
      logActivity(`Made ${user.email} a moderator`, 'admin_action');
      showNotification('User set as moderator. Now assign users to manage.');
    }

    function verifyKYC() {
      if (!appState.currentUser) return;
      
      // Check if current admin can manage this user
      if (!canManageUser(appState.currentUser.id)) {
        showNotification('You do not have permission to verify KYC for this user', 'error');
        return;
      }
      
      appState.currentUser.kycVerified = true;
      logActivity(`Verified KYC for ${appState.currentUser.email}`, 'kyc');
      showNotification('KYC verified successfully');
      
      // Update UI
      document.getElementById('kycStatusText').textContent = '✅ Verified';
      document.getElementById('verifyKYCButtonContainer').classList.add('hidden');
    }

    function viewKYC() {
      if (!appState.currentUser) return;
      
      // Check if current admin can manage this user
      if (!canManageUser(appState.currentUser.id)) {
        showNotification('You do not have permission to view KYC for this user', 'error');
        return;
      }
      
      const kycRequest = appState.kycRequests.find(k => k.email === appState.currentUser.email);
      if (!kycRequest) {
        showNotification('No KYC documents found for this user', 'error');
        return;
      }
      
      appState.currentKYC = JSON.parse(JSON.stringify(kycRequest));
      appState.kycStatus = appState.currentKYC.status;
      appState.rejectionReason = appState.currentKYC.rejectionReason || '';
      
      // Fill the form
      document.getElementById('kycName').textContent = appState.currentKYC.name;
      document.getElementById('kycEmail').textContent = appState.currentKYC.email;
      document.getElementById('kycUsername').textContent = appState.currentKYC.username;
      document.getElementById('kycIdType').textContent = appState.currentKYC.idType;
      document.getElementById('kycIdNumber').textContent = appState.currentKYC.idNumber;
      document.getElementById('kycSubmittedAt').textContent = formatDate(appState.currentKYC.submittedAt);
      document.getElementById('kycDocFront').src = appState.currentKYC.documentFront;
      document.getElementById('kycDocBack').src = appState.currentKYC.documentBack;
      
      // Set KYC status radio
      document.querySelector(`input[name="kycStatus"][value="${appState.kycStatus}"]`).checked = true;
      
      // Handle selfie
      if (appState.currentKYC.selfie) {
        document.getElementById('kycSelfieContainer').classList.remove('hidden');
        document.getElementById('kycSelfie').src = appState.currentKYC.selfie;
      } else {
        document.getElementById('kycSelfieContainer').classList.add('hidden');
      }
      
      // Handle rejection reason
      if (appState.kycStatus === 'rejected') {
        document.getElementById('rejectionReasonContainer').classList.remove('hidden');
        document.getElementById('rejectionReason').value = appState.rejectionReason;
      } else {
        document.getElementById('rejectionReasonContainer').classList.add('hidden');
      }
      
      // Show modal
      document.getElementById('kycDocumentModal').classList.remove('hidden');
    }

    function closeKYCDocumentModal() {
      document.getElementById('kycDocumentModal').classList.add('hidden');
    }

    function saveKYCStatus() {
      if (!appState.currentKYC) return;
      
      // Check if current admin can manage this user
      const user = appState.users.find(u => u.email === appState.currentKYC.email);
      if (!user || !canManageUser(user.id)) {
        showNotification('You do not have permission to modify KYC for this user', 'error');
        return;
      }
      
      const kycStatus = document.querySelector('input[name="kycStatus"]:checked').value;
      const rejectionReason = document.getElementById('rejectionReason').value;
      
      // Update user
      const userIndex = appState.users.findIndex(u => u.email === appState.currentKYC.email);
      if (userIndex !== -1) {
        appState.users[userIndex].kycVerified = kycStatus === 'verified';
      }
      
      // Update KYC request
      const kycIndex = appState.kycRequests.findIndex(k => k.id === appState.currentKYC.id);
      if (kycIndex !== -1) {
        appState.kycRequests[kycIndex].status = kycStatus;
        appState.kycRequests[kycIndex].updatedAt = new Date().toISOString();
        
        if (kycStatus === 'rejected') {
          appState.kycRequests[kycIndex].rejectionReason = rejectionReason;
        }
      }
      
      const action = kycStatus === 'verified' ? 'verified' : kycStatus === 'rejected' ? 'rejected' : 'updated';
      showNotification(`KYC ${action} successfully`);
      logActivity(`${action.charAt(0).toUpperCase() + action.slice(1)} KYC for ${appState.currentKYC.email}`, 'kyc');
      
      // Update current user if it's the same
      if (appState.currentUser && appState.currentUser.email === appState.currentKYC.email) {
        appState.currentUser.kycVerified = kycStatus === 'verified';
        document.getElementById('kycStatusText').textContent = kycStatus === 'verified' ? '✅ Verified' : '❌ Not Verified';
        document.getElementById('verifyKYCButtonContainer').classList.toggle('hidden', kycStatus === 'verified');
      }
      
      closeKYCDocumentModal();
    }

    function openStakingEditModal() {
      if (!appState.currentUser) return;
      
      // Check if current admin can manage this user
      if (!canManageUser(appState.currentUser.id)) {
        showNotification('You do not have permission to modify staking for this user', 'error');
        return;
      }
      
      const today = new Date().toISOString().split('T')[0];
      const endDate = new Date();
      endDate.setDate(endDate.getDate() + 30);
      
      appState.currentStaking = {
        coin: 'ELTR',
        amount: 0,
        apy: 5,
        startDate: today,
        endDate: endDate.toISOString().split('T')[0]
      };
      
      // Fill the form
      document.getElementById('stakingCoin').value = appState.currentStaking.coin;
      document.getElementById('stakingAmount').value = appState.currentStaking.amount;
      document.getElementById('stakingAPY').value = appState.currentStaking.apy;
      document.getElementById('stakingStartDate').value = appState.currentStaking.startDate;
      document.getElementById('stakingEndDate').value = appState.currentStaking.endDate;
      
      document.getElementById('stakingModalTitle').textContent = 'Add Staking';
      document.getElementById('stakingEditModal').classList.remove('hidden');
    }

    function closeStakingEditModal() {
      document.getElementById('stakingEditModal').classList.add('hidden');
    }

    function editStaking(index) {
      if (!appState.currentUser || !appState.currentUser.stakingPositions[index]) return;
      
      // Check if current admin can manage this user
      if (!canManageUser(appState.currentUser.id)) {
        showNotification('You do not have permission to modify staking for this user', 'error');
        return;
      }
      
      appState.editingStakingIndex = index;
      appState.currentStaking = JSON.parse(JSON.stringify(appState.currentUser.stakingPositions[index]));
      
      // Fill the form
      document.getElementById('stakingCoin').value = appState.currentStaking.coin;
      document.getElementById('stakingAmount').value = appState.currentStaking.amount;
      document.getElementById('stakingAPY').value = appState.currentStaking.apy;
      document.getElementById('stakingStartDate').value = appState.currentStaking.startDate;
      document.getElementById('stakingEndDate').value = appState.currentStaking.endDate;
      
      document.getElementById('stakingModalTitle').textContent = 'Edit Staking';
      document.getElementById('stakingEditModal').classList.remove('hidden');
    }

    function removeStaking(index) {
      if (!appState.currentUser || !appState.currentUser.stakingPositions[index]) return;
      
      // Check if current admin can manage this user
      if (!canManageUser(appState.currentUser.id)) {
        showNotification('You do not have permission to modify staking for this user', 'error');
        return;
      }
      
      if (confirm('Are you sure you want to remove this staking position?')) {
        appState.currentUser.stakingPositions.splice(index, 1);
        logActivity(`Removed staking position from ${appState.currentUser.email}`, 'staking');
        showNotification('Staking position removed');
        renderStakingPositions();
      }
    }

    function saveStaking() {
      if (!appState.currentUser) return;
      
      // Check if current admin can manage this user
      if (!canManageUser(appState.currentUser.id)) {
        showNotification('You do not have permission to modify staking for this user', 'error');
        return;
      }
      
      // Get form values
      const coin = document.getElementById('stakingCoin').value;
      const amount = parseFloat(document.getElementById('stakingAmount').value);
      const apy = parseFloat(document.getElementById('stakingAPY').value);
      const startDate = document.getElementById('stakingStartDate').value;
      const endDate = document.getElementById('stakingEndDate').value;
      
      // Validation
      if (!amount || amount <= 0) {
        alert('Please enter a valid amount');
        return;
      }
      
      if (!endDate) {
        alert('Please select an end date');
        return;
      }
      
      // Update current staking
      appState.currentStaking = {
        coin,
        amount,
        apy,
        startDate,
        endDate
      };
      
      if (appState.editingStakingIndex === null) {
        // Add new staking
        appState.currentUser.stakingPositions.push({
          ...appState.currentStaking,
          progress: 0 // New staking starts at 0% progress
        });
        logActivity(`Added staking position to ${appState.currentUser.email}`, 'staking');
        showNotification('Staking position added');
      } else {
        // Update existing staking
        appState.currentUser.stakingPositions[appState.editingStakingIndex] = {
          ...appState.currentStaking,
          progress: appState.currentUser.stakingPositions[appState.editingStakingIndex].progress
        };
        logActivity(`Updated staking position for ${appState.currentUser.email}`, 'staking');
        showNotification('Staking position updated');
      }
      
      closeStakingEditModal();
      renderStakingPositions();
    }

    function openImageModal(imageUrl) {
      document.getElementById('currentImagePreview').src = imageUrl;
      document.getElementById('imagePreviewModal').classList.remove('hidden');
    }

    function closeImageModal() {
      document.getElementById('imagePreviewModal').classList.add('hidden');
    }

    function openTicketReplyModal(ticketId) {
      const ticket = appState.supportTickets.find(t => t.id === ticketId);
      if (!ticket) return;
      
      // Check if current admin can manage this user
      const user = appState.users.find(u => u.email === ticket.email);
      if (!user || !canManageUser(user.id)) {
        showNotification('You do not have permission to reply to this ticket', 'error');
        return;
      }
      
      appState.currentTicket = JSON.parse(JSON.stringify(ticket));
      document.getElementById('ticketEmail').textContent = ticket.email;
      document.getElementById('ticketSubject').textContent = ticket.subject;
      document.getElementById('replyMessage').value = '';
      document.getElementById('ticketReplyModal').classList.remove('hidden');
    }

    function closeTicketReplyModal() {
      document.getElementById('ticketReplyModal').classList.add('hidden');
    }

    function sendTicketReply() {
      if (!appState.currentTicket) return;
      
      // Check if current admin can manage this user
      const user = appState.users.find(u => u.email === appState.currentTicket.email);
      if (!user || !canManageUser(user.id)) {
        showNotification('You do not have permission to reply to this ticket', 'error');
        return;
      }
      
      const replyMessage = document.getElementById('replyMessage').value.trim();
      if (!replyMessage) {
        showNotification('Please enter a reply message', 'error');
        return;
      }
      
      // Find the original ticket
      const index = appState.supportTickets.findIndex(t => t.id === appState.currentTicket.id);
      if (index === -1) return;
      
      // Update the ticket
      appState.supportTickets[index].status = 'pending';
      appState.supportTickets[index].updatedAt = new Date().toISOString();
      appState.supportTickets[index].unread = false;
      
      // Add the reply
      appState.supportTickets[index].replies.push({
        type: 'admin',
        message: replyMessage,
        date: new Date().toISOString()
      });
      
      showNotification('Reply sent successfully');
      logActivity(`Replied to ticket #${appState.currentTicket.id}`, 'support');
      closeTicketReplyModal();
    }

    function openActivityLogsModal() {
      document.getElementById('activityLogsModal').classList.remove('hidden');
      renderLogsTable();
    }

    function closeActivityLogsModal() {
      document.getElementById('activityLogsModal').classList.add('hidden');
    }

    function viewLogDetails(log) {
      appState.currentLog = log;
      
      const typeClass = {
        'admin_action': 'bg-blue-100 text-blue-800',
        'staking': 'bg-green-100 text-green-800',
        'kyc': 'bg-purple-100 text-purple-800',
        'support': 'bg-yellow-100 text-yellow-800',
        'system': 'bg-gray-100 text-gray-800'
      }[log.type] || 'bg-gray-100 text-gray-800';
      
      const detailsContent = document.getElementById('logDetailContent');
      detailsContent.innerHTML = `
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
          <div>
            <h4 class="text-sm font-medium text-gray-500">Timestamp</h4>
            <p class="mt-1 text-sm text-gray-900">${log.time}</p>
          </div>
          <div>
            <h4 class="text-sm font-medium text-gray-500">Type</h4>
            <p class="mt-1">
              <span class="px-2 py-1 text-xs rounded-full ${typeClass}">
                ${log.type.replace('_', ' ').toUpperCase()}
              </span>
            </p>
          </div>
          <div>
            <h4 class="text-sm font-medium text-gray-500">User</h4>
            <p class="mt-1 text-sm text-gray-900">${log.user}</p>
          </div>
          <div>
            <h4 class="text-sm font-medium text-gray-500">Action</h4>
            <p class="mt-1 text-sm text-gray-900">${log.action || 'N/A'}</p>
          </div>
        </div>
        
        <div class="mb-4">
          <h4 class="text-sm font-medium text-gray-500">Message</h4>
          <p class="mt-1 text-sm text-gray-900">${log.message}</p>
        </div>
        
        ${log.details ? `
          <div>
            <h4 class="text-sm font-medium text-gray-500">Details</h4>
            <pre class="mt-1 text-xs p-2 bg-gray-50 rounded overflow-x-auto">${JSON.stringify(log.details, null, 2)}</pre>
          </div>
        ` : ''}
      `;
      
      document.getElementById('logDetailModal').classList.remove('hidden');
    }

    function closeLogDetailModal() {
      document.getElementById('logDetailModal').classList.add('hidden');
    }

    // Pagination Functions
    function prevPage() {
      if (appState.currentPage > 1) {
        appState.currentPage--;
        renderUsersTable();
      }
    }

    function nextPage() {
      const filteredUsers = getFilteredUsers();
      const totalPages = Math.ceil(filteredUsers.length / appState.itemsPerPage);
      
      if (appState.currentPage < totalPages) {
        appState.currentPage++;
        renderUsersTable();
      }
    }

    function prevLogPage() {
      if (appState.logCurrentPage > 1) {
        appState.logCurrentPage--;
        renderLogsTable();
      }
    }

    function nextLogPage() {
      const filteredLogs = getFilteredLogs();
      const totalPages = Math.ceil(filteredLogs.length / appState.logItemsPerPage);
      
      if (appState.logCurrentPage < totalPages) {
        appState.logCurrentPage++;
        renderLogsTable();
      }
    }

    // Event Listeners
    document.addEventListener('DOMContentLoaded', function() {
      // Initialize default dates for staking
      const today = new Date().toISOString().split('T')[0];
      const endDate = new Date();
      endDate.setDate(endDate.getDate() + 30);
      appState.currentStaking.startDate = today;
      appState.currentStaking.endDate = endDate.toISOString().split('T')[0];
      
      // Set default premium expiry (1 year from now) for premium users
      const defaultPremiumExpiry = new Date();
      defaultPremiumExpiry.setFullYear(defaultPremiumExpiry.getFullYear() + 1);
      
      // Update sample users with default premium expiry if they're premium
      appState.users.forEach(user => {
        if (user.isPremium && !user.premiumExpiry) {
          user.premiumExpiry = defaultPremiumExpiry.toISOString().split('T')[0];
        }
        
        // Initialize balances if not set
        if (!user.balances) {
          user.balances = {
            eltr: 0,
            btc: 0,
            eth: 0,
            usdt: 0
          };
        }
      });
      
      // Render initial data
      renderUsersTable();
      renderRecentLogs();
      
      // Modal close handlers
      document.getElementById('userEditModal').addEventListener('click', function(e) {
        if (e.target === this) closeUserEditModal();
      });
      
      document.getElementById('stakingEditModal').addEventListener('click', function(e) {
        if (e.target === this) closeStakingEditModal();
      });
      
      document.getElementById('kycDocumentModal').addEventListener('click', function(e) {
        if (e.target === this) closeKYCDocumentModal();
      });
      
      document.getElementById('imagePreviewModal').addEventListener('click', closeImageModal);
      
      document.getElementById('ticketReplyModal').addEventListener('click', function(e) {
        if (e.target === this) closeTicketReplyModal();
      });
      
      document.getElementById('activityLogsModal').addEventListener('click', function(e) {
        if (e.target === this) closeActivityLogsModal();
      });
      
      document.getElementById('logDetailModal').addEventListener('click', function(e) {
        if (e.target === this) closeLogDetailModal();
      });
      
      // Button click handlers
      document.getElementById('cancelUserEdit').addEventListener('click', closeUserEditModal);
      document.getElementById('saveUserChanges').addEventListener('click', saveUserChanges);
      document.getElementById('verifyKYCButton').addEventListener('click', verifyKYC);
      document.getElementById('viewKYCDocsButton').addEventListener('click', viewKYC);
      document.getElementById('addStakingButton').addEventListener('click', openStakingEditModal);
      document.getElementById('cancelStakingEdit').addEventListener('click', closeStakingEditModal);
      document.getElementById('saveStaking').addEventListener('click', saveStaking);
      document.getElementById('cancelKYCEdit').addEventListener('click', closeKYCDocumentModal);
      document.getElementById('saveKYCStatus').addEventListener('click', saveKYCStatus);
      document.getElementById('cancelTicketReply').addEventListener('click', closeTicketReplyModal);
      document.getElementById('sendTicketReply').addEventListener('click', sendTicketReply);
      document.getElementById('closeActivityLogs').addEventListener('click', closeActivityLogsModal);
      document.getElementById('closeLogDetail').addEventListener('click', closeLogDetailModal);
      document.getElementById('closeLogDetailButton').addEventListener('click', closeLogDetailModal);
      document.getElementById('viewAllLogs').addEventListener('click', openActivityLogsModal);
      
      // Toggle password visibility
      document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordInput = document.getElementById('userPassword');
        appState.showPassword = !appState.showPassword;
        passwordInput.type = appState.showPassword ? 'text' : 'password';
        this.textContent = appState.showPassword ? 'Hide' : 'Show';
      });
      
      // Toggle premium expiry visibility
      document.getElementById('userPremium').addEventListener('change', function() {
        document.getElementById('premiumExpiryContainer').classList.toggle('hidden', !this.checked);
      });
      
      // Toggle rejection reason visibility
      document.querySelectorAll('input[name="kycStatus"]').forEach(radio => {
        radio.addEventListener('change', function() {
          document.getElementById('rejectionReasonContainer').classList.toggle('hidden', this.value !== 'rejected');
        });
      });
      
      // Toggle moderator access visibility
      document.getElementById('userIsModerator').addEventListener('change', function() {
        document.getElementById('moderatorAccessContainer').classList.toggle('hidden', !this.checked);
      });
      
      // Admin/moderator mutual exclusivity
      document.getElementById('userIsAdmin').addEventListener('change', function() {
        if (this.checked) {
          document.getElementById('userIsModerator').checked = false;
          document.getElementById('moderatorAccessContainer').classList.add('hidden');
        }
      });
      
      // Filter change handlers
      document.getElementById('searchQuery').addEventListener('input', filterUsers);
      document.getElementById('filterType').addEventListener('change', filterUsers);
      document.getElementById('filterStatus').addEventListener('change', filterUsers);
      
      document.getElementById('logFilterType').addEventListener('change', filterLogs);
      document.getElementById('logFilterDate').addEventListener('change', filterLogs);
      document.getElementById('logSearchQuery').addEventListener('input', filterLogs);
      
      // Pagination handlers
      document.getElementById('prevPage').addEventListener('click', prevPage);
      document.getElementById('nextPage').addEventListener('click', nextPage);
      document.getElementById('prevLogPage').addEventListener('click', prevLogPage);
      document.getElementById('nextLogPage').addEventListener('click', nextLogPage);
      
      // Image click handlers
      document.getElementById('kycDocFront').addEventListener('click', function() {
        openImageModal(this.src);
      });
      
      document.getElementById('kycDocBack').addEventListener('click', function() {
        openImageModal(this.src);
      });
      
      document.getElementById('kycSelfie').addEventListener('click', function() {
        openImageModal(this.src);
      });
    });
  </script>
</body>
</html>