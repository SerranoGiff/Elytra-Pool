<?php
session_start();

// NO CACHE HEADERS
header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// VALIDATE SESSION
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
  header("Location: ../../index.php?error=Unauthorized access.");
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard | Elytra</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    /* Custom scrollbar */
    ::-webkit-scrollbar {
      width: 6px;
    }

    ::-webkit-scrollbar-track {
      background: transparent;
    }

    ::-webkit-scrollbar-thumb {
      background-color: #888;
      border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb:hover {
      background-color: #555;
    }

    /* Badge styles */
    .badge {
      padding: 0.25rem 0.5rem;
      border-radius: 0.25rem;
      font-size: 0.75rem;
      font-weight: 500;
    }

    .badge-premium {
      background-color: #fef9c3;
      color: #92400e;
    }

    .badge-staking {
      background-color: #dcfce7;
      color: #166534;
    }

    .badge-active {
      background-color: #dbeafe;
      color: #1e40af;
    }

    .badge-banned {
      background-color: #fee2e2;
      color: #991b1b;
    }

    .badge-kyc {
      background-color: #f3e8ff;
      color: #6b21a8;
    }

    .badge-open {
      background-color: #fee2e2;
      color: #991b1b;
    }

    .badge-pending {
      background-color: #fef9c3;
      color: #92400e;
    }

    .badge-resolved {
      background-color: #dcfce7;
      color: #166534;
    }

    .badge-high {
      background-color: #f3e8ff;
      color: #6b21a8;
    }

    .badge-normal {
      background-color: #dbeafe;
      color: #1e40af;
    }

    .badge-admin {
      background-color: #fce7f3;
      color: #9d174d;
    }

    .badge-moderator {
      background-color: #ecfdf5;
      color: #065f46;
    }
  </style>
</head>

<body class="bg-gray-100 min-h-screen">
  <!-- Notification container -->
  <div id="notification-container" class="fixed top-5 right-5 space-y-2 z-50"></div>

  <!-- Navbar -->
  <nav class="bg-gray-800 text-white px-4 py-4 flex items-center justify-between">
    <div class="flex items-center space-x-4">
      <h1 class="text-lg lg:text-xl font-bold">Elytra Admin</h1>
    </div>
    <div class="flex items-center space-x-4">
      <a href="user-dashboard.html" class="bg-blue-600 hover:bg-blue-700 px-3 py-1 rounded flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
        </svg>
        Back to User
      </a>
      <a href="../../config/logout.php" class="bg-red-600 hover:bg-red-700 px-3 py-1 rounded flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd" />
        </svg>
        Logout
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

  <!-- Ticket Reply Modal -->
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

  <script>
    // Sample Data
    const sampleUsers = [{
        id: 1,
        email: "moderator@elytra.com",
        username: "moderator1",
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
        managedUsers: [2, 3] // This moderator can manage user2 and user3
      },
      {
        id: 2,
        email: "user1@elytra.com",
        username: "user1",
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
        stakingPositions: [{
          coin: "ELTR",
          amount: 1000,
          apy: 5.2,
          startDate: "2023-11-01",
          endDate: "2023-12-01",
          progress: 50
        }],
        lastActive: "2023-11-20 09:30:22",
        managedBy: [1] // Managed by moderator1
      },
      {
        id: 3,
        email: "premium_user@elytra.com",
        username: "premium1",
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
        stakingPositions: [{
          coin: "ELTR",
          amount: 5000,
          apy: 6.0,
          startDate: "2023-11-10",
          endDate: "2024-02-10",
          progress: 25
        }],
        lastActive: "2023-11-20 10:05:45",
        managedBy: [1] // Managed by moderator1
      }
    ];

    const sampleLogs = [{
        id: 1,
        time: "2023-11-20 09:30:22",
        message: "User staked 1000 ELTR at 5.2% APY",
        type: "staking",
        user: "user1@elytra.com",
        userId: 2
      },
      {
        id: 2,
        time: "2023-11-20 09:15:10",
        message: "KYC verified for premium_user@elytra.com",
        type: "kyc",
        user: "moderator1@elytra.com",
        userId: 1
      },
      {
        id: 3,
        time: "2023-11-19 15:20:00",
        message: "Balance updated for user1@elytra.com",
        type: "admin_action",
        user: "moderator1@elytra.com",
        userId: 1
      }
    ];

    const sampleKYCs = [{
        id: 1,
        email: "user1@elytra.com",
        username: "user1",
        name: "John Doe",
        idType: "Passport",
        idNumber: "P123456789",
        documentFront: "https://via.placeholder.com/300x180?text=Passport+Front",
        documentBack: "https://via.placeholder.com/300x180?text=Passport+Back",
        selfie: "https://via.placeholder.com/300x180?text=Selfie+with+ID",
        status: "verified",
        submittedAt: "2023-11-18T09:30:22",
        userId: 2
      },
      {
        id: 2,
        email: "premium_user@elytra.com",
        username: "premium1",
        name: "Jane Smith",
        idType: "Driver's License",
        idNumber: "DL987654321",
        documentFront: "https://via.placeholder.com/300x180?text=License+Front",
        documentBack: "https://via.placeholder.com/300x180?text=License+Back",
        selfie: "https://via.placeholder.com/300x180?text=Selfie+with+License",
        status: "verified",
        submittedAt: "2023-11-15T11:45:33",
        userId: 3
      }
    ];

    const sampleTickets = [{
        id: 1001,
        email: 'user1@elytra.com',
        subject: 'Withdrawal not processing',
        message: 'I initiated a withdrawal 3 days ago but it still shows as pending.',
        status: 'open',
        priority: 'high',
        createdAt: '2023-11-18T09:30:22',
        userId: 2
      },
      {
        id: 1002,
        email: 'premium_user@elytra.com',
        subject: 'KYC verification stuck',
        message: 'My KYC documents were submitted a week ago but still show as pending.',
        status: 'pending',
        priority: 'normal',
        createdAt: '2023-11-15T11:45:33',
        userId: 3
      }
    ];

    // App State
    const appState = {
      currentUser: sampleUsers[0], // Moderator user
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
      currentTicket: null,
      showPassword: false,
      kycStatus: 'pending',
      rejectionReason: '',
      replyMessage: '',
      logFilterType: 'all',
      logFilterDate: 'all',
      logSearchQuery: '',
      logCurrentPage: 1,
      logItemsPerPage: 10
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

    function canManageUser(userId) {
      // Moderators can only manage users assigned to them
      return appState.currentUser.isModerator &&
        appState.currentUser.managedUsers.includes(userId);
    }

    // Filter Functions
    function filterUsers() {
      appState.searchQuery = document.getElementById('searchQuery').value.toLowerCase();
      appState.filterType = document.getElementById('filterType').value;
      appState.filterStatus = document.getElementById('filterStatus').value;
      appState.currentPage = 1;
      renderUsersTable();
    }

    function getFilteredUsers() {
      let result = sampleUsers.filter(user =>
        appState.currentUser.isModerator ?
        appState.currentUser.managedUsers.includes(user.id) :
        true
      );

      // Apply search filter
      if (appState.searchQuery) {
        result = result.filter(user =>
          user.email.toLowerCase().includes(appState.searchQuery) ||
          user.username.toLowerCase().includes(appState.searchQuery) ||
          `${user.firstName} ${user.lastName}`.toLowerCase().includes(appState.searchQuery)
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

        // Check if current user can manage this user
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
              `}
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
      document.getElementById('totalUsersCount').textContent = sampleUsers.length;
      document.getElementById('activeStakingCount').textContent = sampleUsers.reduce((count, user) => count + user.stakingPositions.length, 0);
      document.getElementById('premiumUsersCount').textContent = sampleUsers.filter(user => user.isPremium).length;
      document.getElementById('pendingKYCCount').textContent = sampleUsers.filter(user => user.kycRequested && !user.kycVerified).length;
    }

    function renderRecentLogs() {
      const container = document.getElementById('recentLogsContainer');
      container.innerHTML = '';

      const recentLogs = sampleLogs
        .filter(log =>
          appState.currentUser.isModerator ?
          appState.currentUser.managedUsers.includes(log.userId) || log.userId === appState.currentUser.id :
          true
        )
        .sort((a, b) => new Date(b.time) - new Date(a.time))
        .slice(0, 5);

      recentLogs.forEach(log => {
        const logElement = document.createElement('div');
        logElement.className = 'border-b py-1';

        const typeClass = {
          'admin_action': 'text-blue-600',
          'staking': 'text-green-600',
          'kyc': 'text-purple-600',
          'system': 'text-gray-600'
        } [log.type] || 'text-gray-600';

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
      const user = sampleUsers.find(u => u.id === userId);
      if (!user) return;

      // Check if current user can manage this user
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

      // Check if current user can manage this user
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

      // Find the original user and update
      const index = sampleUsers.findIndex(u => u.id === appState.currentUser.id);
      if (index !== -1) {
        sampleUsers[index] = appState.currentUser;
        showNotification('User updated successfully');
        closeUserEditModal();
        renderUsersTable();
      }
    }

    function toggleBanStatus(userId) {
      const user = sampleUsers.find(u => u.id === userId);
      if (!user) return;

      // Check if current user can manage this user
      if (!canManageUser(userId)) {
        showNotification('You do not have permission to modify this user', 'error');
        return;
      }

      user.isBanned = !user.isBanned;
      if (user.isBanned) user.active = false;

      const action = user.isBanned ? 'banned' : 'unbanned';
      showNotification(`User ${action} successfully`);
      renderUsersTable();
    }

    function verifyKYC() {
      if (!appState.currentUser) return;

      // Check if current user can manage this user
      if (!canManageUser(appState.currentUser.id)) {
        showNotification('You do not have permission to verify KYC for this user', 'error');
        return;
      }

      appState.currentUser.kycVerified = true;
      showNotification('KYC verified successfully');

      // Update UI
      document.getElementById('kycStatusText').textContent = '✅ Verified';
      document.getElementById('verifyKYCButtonContainer').classList.add('hidden');
    }

    function viewKYC() {
      if (!appState.currentUser) return;

      // Check if current user can manage this user
      if (!canManageUser(appState.currentUser.id)) {
        showNotification('You do not have permission to view KYC for this user', 'error');
        return;
      }

      const kycRequest = sampleKYCs.find(k => k.email === appState.currentUser.email);
      if (!kycRequest) {
        showNotification('No KYC documents found for this user', 'error');
        return;
      }

      appState.currentKYC = JSON.parse(JSON.stringify(kycRequest));
      appState.kycStatus = appState.currentKYC.status;

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

      // Show modal
      document.getElementById('kycDocumentModal').classList.remove('hidden');
    }

    function closeKYCDocumentModal() {
      document.getElementById('kycDocumentModal').classList.add('hidden');
    }

    function saveKYCStatus() {
      if (!appState.currentKYC) return;

      const kycStatus = document.querySelector('input[name="kycStatus"]:checked').value;
      const rejectionReason = document.getElementById('rejectionReason').value;

      // Update user
      const userIndex = sampleUsers.findIndex(u => u.email === appState.currentKYC.email);
      if (userIndex !== -1) {
        sampleUsers[userIndex].kycVerified = kycStatus === 'verified';
      }

      // Update KYC request
      const kycIndex = sampleKYCs.findIndex(k => k.id === appState.currentKYC.id);
      if (kycIndex !== -1) {
        sampleKYCs[kycIndex].status = kycStatus;

        if (kycStatus === 'rejected') {
          sampleKYCs[kycIndex].rejectionReason = rejectionReason;
        }
      }

      const action = kycStatus === 'verified' ? 'verified' : kycStatus === 'rejected' ? 'rejected' : 'updated';
      showNotification(`KYC ${action} successfully`);
      closeKYCDocumentModal();
    }

    function openStakingEditModal() {
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

      if (confirm('Are you sure you want to remove this staking position?')) {
        appState.currentUser.stakingPositions.splice(index, 1);
        showNotification('Staking position removed');
        renderStakingPositions();
      }
    }

    function saveStaking() {
      if (!appState.currentUser) return;

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
        showNotification('Staking position added');
      } else {
        // Update existing staking
        appState.currentUser.stakingPositions[appState.editingStakingIndex] = {
          ...appState.currentStaking,
          progress: appState.currentUser.stakingPositions[appState.editingStakingIndex].progress
        };
        showNotification('Staking position updated');
      }

      closeStakingEditModal();
      renderStakingPositions();
    }

    function replyToTicket(ticketId) {
      const ticket = sampleTickets.find(t => t.id === ticketId);
      if (!ticket) return;

      // Check if current user can manage this ticket
      const user = sampleUsers.find(u => u.email === ticket.email);
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

      const replyMessage = document.getElementById('replyMessage').value.trim();
      if (!replyMessage) {
        showNotification('Please enter a reply message', 'error');
        return;
      }

      // Find the original ticket
      const index = sampleTickets.findIndex(t => t.id === appState.currentTicket.id);
      if (index === -1) return;

      // Update the ticket
      sampleTickets[index].status = 'pending';

      showNotification('Reply sent successfully');
      closeTicketReplyModal();
    }

    function resolveTicket(ticketId) {
      const ticket = sampleTickets.find(t => t.id === ticketId);
      if (!ticket) return;

      // Check if current user can manage this ticket
      const user = sampleUsers.find(u => u.email === ticket.email);
      if (!user || !canManageUser(user.id)) {
        showNotification('You do not have permission to modify this ticket', 'error');
        return;
      }

      ticket.status = 'resolved';
      showNotification(`Ticket #${ticketId} marked as resolved`);
    }

    function openActivityLogsModal() {
      document.getElementById('activityLogsModal').classList.remove('hidden');
    }

    function closeActivityLogsModal() {
      document.getElementById('activityLogsModal').classList.add('hidden');
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

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
      // Set default premium expiry (1 year from now) for premium users
      const defaultPremiumExpiry = new Date();
      defaultPremiumExpiry.setFullYear(defaultPremiumExpiry.getFullYear() + 1);

      sampleUsers.forEach(user => {
        if (user.isPremium && !user.premiumExpiry) {
          user.premiumExpiry = defaultPremiumExpiry.toISOString().split('T')[0];
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

      document.getElementById('ticketReplyModal').addEventListener('click', function(e) {
        if (e.target === this) closeTicketReplyModal();
      });

      document.getElementById('activityLogsModal').addEventListener('click', function(e) {
        if (e.target === this) closeActivityLogsModal();
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

      // Filter change handlers
      document.getElementById('searchQuery').addEventListener('input', filterUsers);
      document.getElementById('filterType').addEventListener('change', filterUsers);
      document.getElementById('filterStatus').addEventListener('change', filterUsers);

      // Pagination handlers
      document.getElementById('prevPage').addEventListener('click', prevPage);
      document.getElementById('nextPage').addEventListener('click', nextPage);

      // Image click handlers
      document.getElementById('kycDocFront').addEventListener('click', function() {
        window.open(this.src, '_blank');
      });

      document.getElementById('kycDocBack').addEventListener('click', function() {
        window.open(this.src, '_blank');
      });

      document.getElementById('kycSelfie').addEventListener('click', function() {
        window.open(this.src, '_blank');
      });
    });
  </script>

  <script>
    if (!<?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>) {
      window.location.href = '../../index.php';
    }
  </script>

</body>

</html>