
<template>
  <Main active-tab="REPORTS">
    <template v-slot:modals>
      <Reset
          :show-modal="showResetModal"
          @toggle-active="ResetModalHandler"
      />
    </template>
  
    <template v-slot:main>
      <!-- Discrepancy Modal -->
      <div v-if="showDiscrepancyModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-11/12 max-w-4xl max-h-[80vh] overflow-auto">
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">System vs TXT File Discrepancy - {{ formattedCurrentDate }}</h2>
            <button @click="closeDiscrepancyModal" class="text-gray-700 hover:text-gray-900">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
          
          <div v-if="isLoading" class="py-10 text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-gray-900 mx-auto"></div>
            <p class="mt-4">Loading discrepancy data...</p>
          </div>
          
          <div v-else-if="discrepancyError" class="py-10 text-center text-red-500">
            {{ discrepancyError }}
          </div>
          
          <div v-else-if="discrepancyData.length === 0" class="py-10 text-center">
            <p class="text-green-600 font-semibold">No discrepancies found! All TXT file counts match system counts.</p>
          </div>
          
          <div v-else>
            <div class="mb-4 bg-yellow-50 p-3 rounded border border-yellow-300">
              <p class="text-amber-700">
                <span class="font-semibold">Note:</span> Showing discrepancies between system counts and TXT files for {{ formattedCurrentDate }}. 
                Positive difference means system count is higher than TXT file.
              </p>
            </div>
            
            <!-- Missing BW Products Section -->
            <div v-if="missingBwProducts.length > 0" class="mb-4 bg-red-50 p-3 rounded border border-red-300">
              <h3 class="font-semibold text-red-700 mb-2 flex items-center">
                <span class="text-red-600 mr-2">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                  </svg>
                </span>
                Missing BW Products ({{ missingBwProducts.length }})
              </h3>
              <p class="text-red-700 text-sm mb-2">
                The following BW products exist in the system but are missing in TXT files:
              </p>
              <div class="max-h-40 overflow-y-auto">
                <table class="w-full text-sm">
                  <thead class="bg-red-100">
                    <tr>
                      <th class="px-3 py-2 text-left">Store</th>
                      <th class="px-3 py-2 text-left">Item ID</th>
                      <th class="px-3 py-2 text-left">Name</th>
                      <th class="px-3 py-2 text-right">System Count</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="item in missingBwProducts" :key="item.STOREID + '-' + item.ITEMID" class="border-b border-red-100">
                      <td class="px-3 py-1.5">{{ item.STOREID }}</td>
                      <td class="px-3 py-1.5 font-medium">{{ item.ITEMID }}</td>
                      <td class="px-3 py-1.5">{{ item.ITEMNAME }}</td>
                      <td class="px-3 py-1.5 text-right">{{ item.systemCount }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            
            <div class="overflow-x-auto">
              <table class="w-full text-sm text-left text-gray-700">
                <thead class="text-xs text-white uppercase bg-navy">
                  <tr>
                    <th class="px-4 py-2">Date</th>
                    <th class="px-4 py-2">Store ID</th>
                    <th class="px-4 py-2">Store Name</th>
                    <th class="px-4 py-2">Item ID</th>
                    <th class="px-4 py-2">Item Name</th>
                    <th class="px-4 py-2">System Count</th>
                    <th class="px-4 py-2">TXT File Count</th>
                    <th class="px-4 py-2">Difference</th>
                    <th class="px-4 py-2">Status</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="item in discrepancyData" :key="`${item.STOREID}-${item.ITEMID}`" 
                      :class="{
                        'bg-red-100': item.status === 'Discrepancy' || item.status === 'Missing in System',
                        'bg-orange-100': item.status === 'Missing in TXT File',
                        'bg-green-100': item.status === 'Match'
                      }">
                    <td class="px-4 py-2 border">{{ item.DATE }}</td>
                    <td class="px-4 py-2 border">{{ item.STOREID }}</td>
                    <td class="px-4 py-2 border">{{ item.STORENAME }}</td>
                    <td class="px-4 py-2 border">{{ item.ITEMID }}</td>
                    <td class="px-4 py-2 border">{{ item.ITEMNAME }}</td>
                    <td class="px-4 py-2 border">{{ item.systemCount }}</td>
                    <td class="px-4 py-2 border">{{ item.fileCount }}</td>
                    <td class="px-4 py-2 border font-medium" 
                        :class="{'text-red-600': item.difference > 0, 'text-blue-600': item.difference < 0}">
                      {{ item.difference }}
                    </td>
                    <td class="px-4 py-2 border" 
                        :class="{
                          'text-red-600': item.status === 'Discrepancy' || item.status === 'Missing in System', 
                          'text-orange-600': item.status === 'Missing in TXT File',
                          'text-green-600': item.status === 'Match'
                        }">
                      {{ item.status }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            
            <!-- Summary stats -->
            <div class="mt-4 bg-gray-50 p-4 rounded border">
              <h3 class="font-semibold text-gray-700 mb-2">Summary</h3>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white p-3 rounded shadow">
                  <p class="text-sm text-gray-600">Total Discrepancies</p>
                  <p class="text-2xl font-bold text-red-600">{{ discrepancyData.length }}</p>
                </div>
                <div class="bg-white p-3 rounded shadow">
                  <p class="text-sm text-gray-600">System > TXT File</p>
                  <p class="text-2xl font-bold text-red-600">
                    {{ discrepancyData.filter(item => item.difference > 0).length }}
                  </p>
                </div>
                <div class="bg-white p-3 rounded shadow">
                  <p class="text-sm text-gray-600">System < TXT File</p>
                  <p class="text-2xl font-bold text-blue-600">
                    {{ discrepancyData.filter(item => item.difference < 0).length }}
                  </p>
                </div>
              </div>
              
              <!-- New section for total comparison -->
              <h3 class="font-semibold text-gray-700 mt-4 mb-2">Total TXT File vs Total Store Orders</h3>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white p-3 rounded shadow">
                  <p class="text-sm text-gray-600">Total System Orders</p>
                  <p class="text-2xl font-bold text-gray-800">{{ discrepancyTotals.systemTotal }}</p>
                </div>
                <div class="bg-white p-3 rounded shadow">
                  <p class="text-sm text-gray-600">Total TXT File Orders</p>
                  <p class="text-2xl font-bold text-gray-800">{{ discrepancyTotals.txtFileTotal }}</p>
                </div>
                <div class="bg-white p-3 rounded shadow">
                  <p class="text-sm text-gray-600">Difference</p>
                  <p class="text-2xl font-bold" 
                     :class="{
                       'text-red-600': (discrepancyTotals.systemTotal - discrepancyTotals.txtFileTotal) > 0,
                       'text-blue-600': (discrepancyTotals.systemTotal - discrepancyTotals.txtFileTotal) < 0,
                       'text-green-600': (discrepancyTotals.systemTotal - discrepancyTotals.txtFileTotal) === 0
                     }">
                    {{ discrepancyTotals.systemTotal - discrepancyTotals.txtFileTotal }}
                  </p>
                </div>
              </div>
            </div>
            
            <!-- <div class="mt-6 flex justify-end">
              <button @click="closeDiscrepancyModal" class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 mr-2">
                Close
              </button>
              <button @click="recoverDiscrepancies" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 mr-2 flex items-center" :disabled="isRecovering">
                <span v-if="isRecovering" class="mr-2">
                  <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                </span>
                <span>Recover & Fix TXT Files</span>
              </button>
              <button @click="exportDiscrepancyToExcel(discrepancyData)" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                Export to Excel
              </button>
            </div> -->

            <div class="mt-6 flex justify-end">
              <button @click="closeDiscrepancyModal" class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 mr-2">
                Close
              </button>
              <!-- <button @click="recoverDiscrepancies" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 mr-2 flex items-center" :disabled="isRecovering">
                <span v-if="isRecovering" class="mr-2">
                  <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                </span>
                <span>Recover & Fix TXT Files</span>
              </button> -->
              <button @click="updateSystemFromTxtFiles" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 mr-2 flex items-center" :disabled="isRecovering">
                <span v-if="isRecovering" class="mr-2">
                  <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                </span>
                <span>Update System from TXT</span>
              </button>
              <button @click="exportDiscrepancyToExcel(discrepancyData)" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                Export to Excel
              </button>
            </div>
          </div>
          </div>
        </div>
  
      <TableContainer>
        <div class="absolute adjust">
  
          <div class="flex justify-start items-center">
           
            <form @submit.prevent="submitForm" class="px-2 py-3 max-h-[50vh] lg:max-h-[70vh] overflow-y-auto">
                <input type="hidden" name="_token" :value="$page.props.csrf_token">
                <div date-rangepicker class="flex items-center">
                <div class="relative ml-5 ">
                    <div class="flex absolute inset-y-0 left-0 items-center pl-3 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path></svg>
                    </div>
  
                <input
                id="StartDate"
                type="date"
                v-model="form.StartDate"
                @input="formattedDate1"
                :placeholder="formattedDate1"
                class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Select date start"
                required
                />
                <InputError :message="form.errors.StartDate" class="mt-2" />
                </div>
  
                <span class="mx-4 text-gray-500">to</span>
  
                <div class="relative">
                    <div class="flex absolute inset-y-0 left-0 items-center pl-3 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path></svg>
                    </div>
  
                    <input
                    id="EndDate"
                    type="date"
                    v-model="form.EndDate"
                    @input="formattedDate2"
                    :placeholder="formattedDate2"
                    class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Select date end"
                    required
                    />
                    <InputError :message="form.errors.EndDate" class="mt-2" />
                </div>
            </div>
            </form>
  
            <TransparentButton type="submit" @click="submitForm" :disabled="form.processing" :class="{ 'opacity-25': form.processing }">
                <Search class="h-8" />
            </TransparentButton>
  
            <!-- &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<button @click="generateAndDownloadTextFile">Generate and Download Text File</button> -->
  
            <details className="dropdown">
              <summary className="btn m-1 !bg-navy !text-white">TYPES</summary>
              <ul className="menu dropdown-content !bg-gray-100 rounded-box z-[1] w-52 p-2 shadow">
                <li><a href="/orderingconso">BW PRODUCTS</a></li>
                <li><a href="/warehouse-reports">WAREHOUSE</a></li>
              </ul>
            </details>
                
            <details className="dropdown">
              <summary className="btn m-1 !bg-navy !text-white">RECENT</summary>
              <ul className="menu dropdown-content !bg-gray-100 rounded-box z-[1] w-52 p-2 shadow">
                <li><a href="/lastmonth">Last Month</a></li>
                <li><a href="/lastweek">Last Week</a></li>
                <li><a href="/yesterday">Yesterday</a></li>
              </ul>
            </details>
  
            <details class="dropdown">
              <summary class="btn m-1 bg-navy text-white">-</summary>
              <ul v-if="isAdmin" class="menu dropdown-content bg-gray-100 rounded-box z-[1] w-52 p-2 shadow overflow-y-auto max-h-[50vh]">
                <li v-for="or in noorders" :key="or.NAME" :value="or.NAME">
                  <a>{{ or.NAME }}</a>
                </li>
              </ul>
            </details>
  
            <!-- Discrepancy Button with current date indicator -->
            <PrimaryButton
                type="button"
                @click="toggleDiscrepancyModal"
                class="m-1 !bg-amber-600 hover:!bg-amber-700 flex items-center"
            >
              <span>DISCREPANCY</span>
              <span class="ml-2 bg-white text-amber-700 text-xs px-2 py-0.5 rounded-full">Today</span>
            </PrimaryButton>
  
            <SuccessButton
                  type="button"
                  @click="exportToExcel"
                  class="m-6 bg-green"
                >
                  <ExcelIcon class="h-4" />
            </SuccessButton>
  
            <PrimaryButton
                type="button"
                @click="toggleResetModal"
                class="m-1 ml-2 !bg-navy p-10"
            >
                      RESET
            </PrimaryButton>
  
            <PrimaryButton
                  type="button"
                  v-if="isAdmin"
                  @click="SYNCFG"
                  class="bg-red-900"
                >
                   <Refresh class="h-4" />
            </PrimaryButton>
  
            <!-- <PrimaryButton
                  type="button"
                  v-if="isAdmin"
                  @click="FIXED"
                  class="bg-red-900 ml-2"
                >
                   <Warning class="h-4" />
            </PrimaryButton> -->
  
            <!-- <h6 class="ml-2 font-bold">BW PRODUCTS</h6> -->
            <p class="font-bold text:navy font-xs">{{ startDate }} | {{ endDate }}</p>
            
          </div>
        </div>
        
        <div class="custom-datatable">
          <DataTable :data="flattenedOrders" :columns="columns" class="w-full relative display" :options="options">
            <template #action="data">
            <div class="flex justify-start">
            </div>
          </template>
        </DataTable>
        </div>
  
        <!-- <footer class="mt-4 p-4 bg-gray-100 rounded-b-lg">
          <p class="text-right font-bold">Grand Total: {{ grandTotal }}</p>
        </footer> -->
        
      </TableContainer>
    </template>
  </Main>
  </template>
  
  <style>
div.dt-scroll-foot{
  font-weight: bold;
  background-color: #7c0000;
  color: #ddd;
}

.table.dataTable thead tr > .dtfc-fixed-start, table.dataTable thead tr > .dtfc-fixed-end, table.dataTable tfoot tr > .dtfc-fixed-start, table.dataTable tfoot tr > .dtfc-fixed-end {
    top: 0;
    bottom: 0;
    z-index: 3;
    background-color: #02721a;
}
</style>

<script setup>
import { defineProps } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Reset from "@/Components/Resetter/reset.vue";
import Main from "@/Layouts/AdminPanel.vue";
import TableContainer from "@/Components/Tables/TableContainer.vue";
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net';
import SuccessButton from "@/Components/Buttons/SuccessButton.vue";
import TransparentButton from "@/Components/Buttons/TransparentButton.vue";
import PrimaryButton from "@/Components/Buttons/PrimaryButton.vue";
import ExcelIcon from "@/Components/Svgs/Excel.vue";
import Search from "@/Components/Svgs/SearchColored.vue";
import Refresh from "@/Components/Svgs/Refresh.vue";
import Warning from "@/Components/Svgs/Warning.vue";
import { ref, computed, isRef, unref, toRefs, onMounted } from 'vue';
import ExcelJS from 'exceljs';
DataTable.use(DataTablesCore);

const emit = defineEmits();

const props = defineProps({
  orders: {
    type: Array,
    required: true,
  },
  auth: {
    type: Object,
    required: true,
  },
  noorders: {
    type: Array,
    required: true,
  },
});

const form = useForm({
  StartDate: '',
  EndDate: '',
});

const submitForm = () => {
  form.get(route('orderingconso.getrange'), {
    preserveScroll: true,
  });
};

const toggleActive = () => {
  emit('toggleActive');
};

const groupedOrders = computed(() => {
  const grouped = props.orders.reduce((acc, order) => {
    const { STORENAME, ITEMID, ITEMNAME, CATEGORY, COUNTED, STOREID, stocks, movementstocks } = order;
    const counted = parseInt((COUNTED ?? '').trim(), 10) || 0;

    // Skip this order if ITEMID is null or undefined
    if (ITEMID == null) return acc;

    if (!acc[ITEMID]) {
      acc[ITEMID] = {
        ITEMID,
        ITEMNAME,
        CATEGORY,
        stocks: parseInt(stocks) || 0,
        movementstocks: parseInt(movementstocks) || 0,
        TOTAL: 0,
      };
    }

    if (!acc[ITEMID][STORENAME]) {
      acc[ITEMID][STORENAME] = { count: 0, STOREID };
    }

    acc[ITEMID][STORENAME].count += counted;
    acc[ITEMID].TOTAL += counted;
    acc[ITEMID].BalanceCount = acc[ITEMID].stocks - acc[ITEMID].TOTAL;

    return acc;
  }, {});

  // Convert the grouped object to an array
  const groupedArray = Object.values(grouped);

  // Custom sorting function
  const customSort = (a, b) => {
    const aID = a.ITEMID || '';
    const bID = b.ITEMID || '';
    
    // Check if both IDs start with "BW"
    if (aID.startsWith("BW") && bID.startsWith("BW")) {
      const aNum = parseInt(aID.slice(2));
      const bNum = parseInt(bID.slice(2));
      
      // If both numbers are less than or equal to 10, sort numerically
      if (aNum <= 10 && bNum <= 10) {
        return aNum - bNum;
      }
      // If one is less than or equal to 10 and the other isn't, the smaller one comes first
      else if (aNum <= 10) {
        return -1;
      }
      else if (bNum <= 10) {
        return 1;
      }
    }
    
    // For all other cases, use default string comparison
    return aID.localeCompare(bID);
  };

  // Sort the array
  return groupedArray.sort(customSort);
});

const flattenedOrders = computed(() => {
  return Object.values(groupedOrders.value);
});

const storeNames = computed(() => {
  const names = new Set();
  flattenedOrders.value.forEach(order => {
    Object.keys(order).forEach(key => {
      if (typeof order[key] === 'object' && order[key] !== null && 'count' in order[key]) {
        names.add(key);
      }
    });
  });
  return Array.from(names).sort((a, b) => {
    const aId = a.split(' - ')[0];
    const bId = b.split(' - ')[0];
    return aId.localeCompare(bId, undefined, { numeric: true, sensitivity: 'base' });
  });
});

const columnTotals = computed(() => {
  const totals = {
    ITEMID: 'Grand Total',
    ITEMNAME: '',
    CATEGORY: '',
    movementstocks: 0,
    stocks: 0,
    BalanceCount: 0,
    TOTAL: 0
  };

  storeNames.value.forEach(store => {
    totals[store] = 0;
  });

  flattenedOrders.value.forEach(order => {
    totals.TOTAL += order.TOTAL;
    totals.movementstocks += order.movementstocks;
    totals.stocks += order.stocks;
    totals.BalanceCount += order.BalanceCount;

    storeNames.value.forEach(store => {
      totals[store] += order[store]?.count || 0;
    });
  });

  return totals;
});

const columns = computed(() => {
  const baseColumns = [
    { 
      title: 'ITEMID', 
      data: 'ITEMID',
      className: 'frozen-column',
      footer: () => columnTotals.value.ITEMID,
      width: '120px'
    },
    { 
      title: 'ITEMS', 
      data: 'ITEMNAME',
      className: 'frozen-column',
      footer: () => '',
      orderable: true,
      width: '200px'
    },
    { 
      title: 'CATEGORY', 
      data: 'CATEGORY',
      className: 'frozen-column',
      footer: () => '',
      width: '120px'
    },
    {
      title: 'STOCKS(SYNC)',
      data: 'stocks',
      className: 'frozen-column',
      footer: () => columnTotals.value.stocks,
      width: '100px'
    },
    {
      title: 'REMAINING STOCKS',
      data: 'BalanceCount',
      className: 'frozen-column',
      footer: () => columnTotals.value.BalanceCount,
      width: '150px'
    },
    {
      title: 'TOTAL',
      data: 'TOTAL',
      footer: () => columnTotals.value.TOTAL
    }
  ];

  const storeColumns = [];
  const storeSet = new Set();

  flattenedOrders.value.forEach(order => {
    Object.entries(order).forEach(([key, value]) => {
      if (typeof value === 'object' && value !== null && 'count' in value && 'STOREID' in value) {
        if (!storeSet.has(key)) {
          storeSet.add(key);
          storeColumns.push({
            title: `${value.STOREID}<br>${key}`,
            data: key,
            render: (data, type) => {
              if (type === 'sort' || type === 'type') {
                return data?.count || 0;
              }
              if (type === 'display') {
                return `${data?.count || 0}`;
              }
              return data?.count || 0;
            },
            footer: () => columnTotals.value[key]
          });
        }
      }
    });
  });

  storeColumns.sort((a, b) => {
    const aId = a.title.split('<br>')[0];
    const bId = b.title.split('<br>')[0];
    return aId.localeCompare(bId, undefined, { numeric: true, sensitivity: 'base' });
  });

  return [...baseColumns, ...storeColumns];
});

const options = {
  paging: false,
  scrollX: true,
  scrollY: "60vh",
  scrollCollapse: true,
  fixedColumns: {
    start: 4,
  },
  error: function (xhr, error, thrown) {
    console.error("DataTables error:", error);
  },
  footerCallback: function(tfoot, data, start, end, display) {
    const api = this.api();
    const footerRow = api.table().footer().querySelectorAll('th');
    columns.value.forEach((column, index) => {
      if (column.footer) {
        footerRow[index].textContent = column.footer();
      }
    });
  },
};

const StartDate = ref(null);
const formattedDate1 = computed(() => {
  if (StartDate.value) {
    const date = new Date(StartDate.value);
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
  }
  return '';
});

const EndDate = ref(null);
const formattedDate2 = computed(() => {
  if (EndDate.value) {
    const date = new Date(EndDate.value);
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
  }
  return '';
});

const currentDate = computed(() => {
  const now = new Date();
  const year = now.getFullYear();
  const month = String(now.getMonth() + 1).padStart(2, '0');
  const day = String(now.getDate()).padStart(2, '0');
  return `${year}${month}${day}`;
});

// Formatted current date for display
const formattedCurrentDate = computed(() => {
  const now = new Date();
  return now.toLocaleDateString('en-US', { 
    year: 'numeric', 
    month: 'long', 
    day: 'numeric' 
  });
});

function generateTextFileContent(flattenedOrders, columns) {
  const headerRow = columns.map(column => column.title).join(',');
  const dataRows = flattenedOrders.map(order => {
    const rowData = columns.map(column => order[column.data] || '');
    return rowData.join(',');
  });

  return [headerRow, ...dataRows].join('\n');
}

function downloadTextFile(filename, content) {
  const blob = new Blob([content], { type: 'text/plain;charset=utf-8' });
  const url = URL.createObjectURL(blob);
  const link = document.createElement('a');
  link.href = url;
  link.download = filename;
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
  URL.revokeObjectURL(url);
}

function generateAndDownloadTextFile() {
  const filename = `BW0001${isRef(currentDate) ? unref(currentDate) : currentDate.value}.txt`;
  const content = generateTextFileContent(flattenedOrders.value, columns.value);
  downloadTextFile(filename, content);
}

// State for the discrepancy modal
const showDiscrepancyModal = ref(false);
const discrepancyData = ref([]);
const isLoading = ref(false);
const discrepancyError = ref(null);

// State for the discrepancy totals
const discrepancyTotals = ref({
  systemTotal: 0,
  txtFileTotal: 0
});

// Add missing BW products reference
const missingBwProducts = ref([]);

// State for fetched TXT files
const txtFileData = ref({});
const isLoadingTxtFiles = ref(false);
const currentDateTime = ref('');
const storeList = ref([]);

const isRecovering = ref(false);


const recoverDiscrepancies = async () => {
  try {
    if (discrepancyData.value.length === 0 && missingBwProducts.value.length === 0) {
      alert("No discrepancies to recover.");
      return;
    }

    // Confirm the action
    const confirmed = confirm("This will replace all TXT files based on system data to resolve discrepancies. Continue?");
    if (!confirmed) return;

    isRecovering.value = true;

    // Get the current date in format YYYYMMDD
    const now = new Date();
    const dateStr = `${now.getFullYear()}${String(now.getMonth() + 1).padStart(2, '0')}${String(now.getDate()).padStart(2, '0')}`;
    
    // Build the data structure for recovery
    const storeDiscrepancies = {};
    
    // Process all discrepancies to get corrected counts
    [...discrepancyData.value, ...missingBwProducts.value].forEach(item => {
      const storeId = item.STOREID || '';
      if (!storeId || storeId === 'undefined') {
        console.warn(`Skipping item with invalid store ID: ${item.ITEMID}`);
        return;
      }
      
      if (!storeDiscrepancies[storeId]) {
        storeDiscrepancies[storeId] = {
          items: {},
          date: item.DATE || currentDateTime.value
        };
      }
      
      // Always use system count, as we want to correct the TXT files
      storeDiscrepancies[storeId].items[item.ITEMID] = item.systemCount;
    });
    
    // Add the corrected counts directly from your flat orders (for items missing from TXT)
    flattenedOrders.value.forEach(order => {
      // For each store in this order
      Object.entries(order).forEach(([key, value]) => {
        if (typeof value === 'object' && value !== null && 'count' in value && 'STOREID' in value) {
          const storeId = value.STOREID;
          const count = value.count;
          
          // Skip if count is zero or undefined
          if (!count) return;
          
          if (!storeDiscrepancies[storeId]) {
            storeDiscrepancies[storeId] = {
              items: {},
              date: currentDateTime.value
            };
          }
          
          // Add the item with its count (this will override any previous value)
          storeDiscrepancies[storeId].items[order.ITEMID] = count;
        }
      });
    });
    
    console.log('Store discrepancies data for recovery:', storeDiscrepancies);
    
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Send data to server to recreate TXT files
    const response = await fetch(`/api/recover-txt-files/${dateStr}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json',
        'Cache-Control': 'no-cache, no-store, must-revalidate',
        'Pragma': 'no-cache',
        'Expires': '0'
      },
      credentials: 'same-origin',
      body: JSON.stringify({ storeData: storeDiscrepancies })
    });
    
    if (!response.ok) {
      const errorData = await response.json().catch(() => null);
      const errorText = await response.text().catch(() => 'Unknown error');
      throw new Error(`Server returned ${response.status}: ${errorData?.message || errorText}`);
    }
    
    const result = await response.json();
    console.log('Recovery response:', result);
    
    // Close the modal first
    closeDiscrepancyModal();
    
    // Show success message
    alert(`Recovery completed successfully!
• ${result.filesDeleted || 0} existing files deleted
• ${result.recoveredFiles || 0} new TXT files created
    
Please wait a moment before checking the files again.`);
    
    // Add a delay to allow file system operations to complete
    setTimeout(async () => {
      try {
        // Reopen the modal with fresh data
        await toggleDiscrepancyModal();
      } catch (verifyError) {
        console.error('Error refreshing data:', verifyError);
      }
      
      isRecovering.value = false;
    }, 3000); // 3 second delay
    
  } catch (error) {
    console.error('Error recovering discrepancies:', error);
    alert(`Error recovering discrepancies: ${error.message}`);
    isRecovering.value = false;
  }
};


// Function to fetch and parse TXT files from the server
const fetchTxtFiles = async () => {
  isLoadingTxtFiles.value = true;
  try {
    // Get the current date in format YYYYMMDD
    const now = new Date();
    const dateStr = `${now.getFullYear()}${String(now.getMonth() + 1).padStart(2, '0')}${String(now.getDate()).padStart(2, '0')}`;
    currentDateTime.value = now.toISOString().split('T')[0]; // YYYY-MM-DD format
    
    console.log('Fetching text files for date:', dateStr);
    
    // Make actual API call to fetch text file data from the file system
    const response = await fetch(`/api/get-txt-files/${dateStr}`);
    
    if (!response.ok) {
      console.error('Failed to fetch TXT files:', response.status, response.statusText);
      throw new Error(`Failed to fetch TXT file data: ${response.status} ${response.statusText}`);
    }
    
    const data = await response.json();
    console.log('Received TXT file data:', data);
    
    // Get the list of stores with orders
    const storesResponse = await fetch(`/api/get-stores-with-orders/${dateStr}`);
    if (storesResponse.ok) {
      const storesData = await storesResponse.json();
      storeList.value = storesData.stores || [];
      console.log('Store list:', storeList.value);
    } else {
      console.error('Failed to fetch store list:', storesResponse.status, storesResponse.statusText);
    }
    
    txtFileData.value = data.txtFileData || {};
    isLoadingTxtFiles.value = false;
    return data.txtFileData;
  } catch (error) {
    console.error('Error fetching TXT files:', error);
    isLoadingTxtFiles.value = false;
    throw error;
  }
};

const getCSRFToken = () => {
  return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
};

const updateSystemFromTxtFiles = async () => {
  try {
    // Confirm the action
    const confirmed = confirm("This will update system COUNTED values based on TXT files. Continue?");
    if (!confirmed) return;
    
    isRecovering.value = true;
    
    // Get the current date in format YYYYMMDD
    const now = new Date();
    const dateStr = `${now.getFullYear()}${String(now.getMonth() + 1).padStart(2, '0')}${String(now.getDate()).padStart(2, '0')}`;
    
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Make API call to update system from TXT files
    const response = await fetch(`/api/update-system-from-txt/${dateStr}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json'
      },
      credentials: 'same-origin'
    });
    
    if (!response.ok) {
      const errorData = await response.json().catch(() => null) || { message: `Server returned ${response.status}` };
      throw new Error(errorData.message);
    }
    
    const result = await response.json();
    console.log('System update response:', result);
    
    // Close the modal first
    closeDiscrepancyModal();
    
    // Show success message
    alert(`System update completed successfully!
• ${result.filesParsed || 0} TXT files processed
• ${result.recordsUpdated || 0} system records updated
• Stores processed: ${result.storesProcessed ? result.storesProcessed.join(', ') : 'None'}
    
The page will refresh to show updated data.`);
    
    // Refresh the page to show updated data
    setTimeout(() => {
      window.location.reload();
    }, 1500);
    
  } catch (error) {
    console.error('Error updating system from TXT files:', error);
    alert(`Error updating system from TXT files: ${error.message}`);
    isRecovering.value = false;
  }
};

const toggleDiscrepancyModal = async () => {
  showDiscrepancyModal.value = true;
  isLoading.value = true;
  discrepancyError.value = null;
  
  try {
    // Create formatted date string in YYYYMMDD format
    const now = new Date();
    const dateStr = `${now.getFullYear()}${String(now.getMonth() + 1).padStart(2, '0')}${String(now.getDate()).padStart(2, '0')}`;
    currentDateTime.value = now.toISOString().split('T')[0]; // YYYY-MM-DD format
    
    console.log('Fetching text files for date:', dateStr);
    
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Use the correct URL with /api prefix
    let apiUrl = `${window.location.origin}/api/get-txt-files/${dateStr}`;
    console.log('Attempting to fetch from:', apiUrl);
    
    const response = await fetch(apiUrl, {
      method: 'GET',
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': csrfToken
      },
      credentials: 'same-origin'
    });
    
    // Log response details for debugging
    console.log('Response status:', response.status);
    
    // Check if it's JSON
    const contentType = response.headers.get('content-type');
    if (!contentType || !contentType.includes('application/json')) {
      const text = await response.text();
      console.error('Non-JSON response:', text.substring(0, 500));
      throw new Error(`Server returned non-JSON response (${response.status} ${response.statusText})`);
    }
    
    const data = await response.json();
    console.log('Parsed JSON data:', data);
    
    // Now fetch store list with the correct URL
    const storesUrl = `${window.location.origin}/api/get-stores-with-orders/${dateStr}`;
    console.log('Fetching stores from:', storesUrl);
    
    const storesResponse = await fetch(storesUrl, {
      method: 'GET',
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': csrfToken
      },
      credentials: 'same-origin'
    });
    
    if (!storesResponse.ok) {
      const storesText = await storesResponse.text();
      console.error('Stores API error:', storesText.substring(0, 500));
      throw new Error(`Stores API error: ${storesResponse.status} ${storesResponse.statusText}`);
    }
    
    const storesData = await storesResponse.json();
    console.log('Stores data:', storesData);
    
    // Process responses
    storeList.value = storesData.stores || [];
    txtFileData.value = data.txtFileData || {};
    
    // Generate discrepancy data and get totals
    const result = generateDiscrepancyData(txtFileData.value);
    discrepancyData.value = result.discrepancies;
    discrepancyTotals.value = result.totals;
    missingBwProducts.value = result.missingBwProducts;
    
    isLoading.value = false;
    
  } catch (error) {
    console.error('Error in discrepancy check:', error);
    discrepancyError.value = error.message || 'Failed to fetch discrepancy data. Please try again.';
    isLoading.value = false;
  }
};

const generateDiscrepancyData = (txtData) => {
  console.log('Generating discrepancy data');
  console.log('TXT data:', txtData);
  console.log('Flattened orders:', flattenedOrders.value);
  
  // Create discrepancies based on the comparison
  const discrepancies = [];
  
  // Add totals tracking
  const totals = {
    systemTotal: 0,
    txtFileTotal: 0
  };
  
  // Track BW products missing in TXT files
  const missingBwProducts = [];
  
  // If there's no TXT data or no system data, return empty array
  if (!txtData || Object.keys(txtData).length === 0 || !flattenedOrders.value || flattenedOrders.value.length === 0) {
    console.log('No data to compare - returning empty discrepancies');
    return { discrepancies, totals, missingBwProducts };
  }
  
  // Loop through each store's data in the TXT files
  Object.entries(txtData).forEach(([storeId, storeData]) => {
    if (!storeData || !storeData.items) {
      console.log(`No valid data for store ${storeId}`);
      return;
    }
    
    // Find store name from store list
    const storeInfo = storeList.value.find(store => store.STOREID === storeId);
    const storeName = storeInfo ? storeInfo.NAME : storeId;
    
    // Loop through each item in the store's data
    Object.entries(storeData.items).forEach(([itemId, fileCount]) => {
      // Convert to number
      const fileCountNum = parseInt(fileCount, 10);
      
      // Add to txtFileTotal
      totals.txtFileTotal += fileCountNum;
      
      // Find the corresponding system data
      const systemOrder = flattenedOrders.value.find(order => order.ITEMID === itemId);
      
      if (systemOrder) {
        // Find the store's data in this order
        const storeColumn = Object.entries(systemOrder).find(([key, value]) => {
          // Check if this is a store column and if it matches the current storeId
          return (
            typeof value === 'object' && 
            value !== null && 
            'count' in value && 
            'STOREID' in value && 
            value.STOREID === storeId
          );
        });
        
        // Get system count from store column or use TOTAL as fallback
        const systemCount = storeColumn ? storeColumn[1].count : systemOrder.TOTAL || 0;
        
        // Convert both to numbers to ensure proper comparison
        const systemCountNum = parseInt(systemCount, 10);
        
        // Add to systemTotal
        totals.systemTotal += systemCountNum;
        
        // Calculate the difference and check strictly for inequality
        const difference = systemCountNum - fileCountNum;
        
        // Add discrepancy if counts don't match
        if (difference !== 0) {
          discrepancies.push({
            STOREID: storeId,
            STORENAME: storeName,
            DATE: storeData.date || currentDateTime.value,
            ITEMID: itemId,
            ITEMNAME: systemOrder.ITEMNAME || 'Unknown Item',
            systemCount: systemCountNum,
            fileCount: fileCountNum,
            difference,
            status: 'Discrepancy'
          });
        }
      } else {
        // Item in TXT file but not in system
        discrepancies.push({
          STOREID: storeId,
          STORENAME: storeName,
          DATE: storeData.date || currentDateTime.value,
          ITEMID: itemId,
          ITEMNAME: 'Unknown Item',
          systemCount: 0,
          fileCount: fileCountNum,
          difference: -fileCountNum,
          status: 'Missing in System'
        });
      }
    });
    
    // Check for items in system but not in TXT file
    flattenedOrders.value.forEach(order => {
      // Find if this order has data for this store
      const storeColumn = Object.entries(order).find(([key, value]) => {
        return (
          typeof value === 'object' && 
          value !== null && 
          'count' in value && 
          'STOREID' in value && 
          value.STOREID === storeId
        );
      });
      
      if (storeColumn && storeColumn[1].count > 0) {
        const systemCountNum = parseInt(storeColumn[1].count, 10);
        
        // Check if this item is missing from the TXT file
        const itemExists = txtData[storeId]?.items && txtData[storeId].items[order.ITEMID] !== undefined;
        
        if (!itemExists) {
          // Add to systemTotal for items missing in TXT file
          totals.systemTotal += systemCountNum;
          
          const discrepancyItem = {
            STOREID: storeId,
            STORENAME: storeName,
            DATE: txtData[storeId]?.date || currentDateTime.value,
            ITEMID: order.ITEMID,
            ITEMNAME: order.ITEMNAME || 'Unknown Item',
            systemCount: systemCountNum,
            fileCount: 0,
            difference: systemCountNum,
            status: 'Missing in TXT File'
          };
          
          discrepancies.push(discrepancyItem);
          
          // Check if this is a BW product (specifically track BW products)
          if (order.ITEMID && order.ITEMID.startsWith('BW')) {
            missingBwProducts.push({
              ...discrepancyItem,
              isBwProduct: true
            });
          }
        }
      }
    });
  });
  
  console.log(`Generated ${discrepancies.length} discrepancies`);
  console.log('Totals:', totals);
  console.log('Missing BW products:', missingBwProducts.length);
  
  // Sort by store ID, then by item ID
  return { 
    discrepancies: discrepancies.sort((a, b) => {
      if (a.STOREID !== b.STOREID) {
        return a.STOREID.localeCompare(b.STOREID, undefined, { numeric: true });
      }
      return a.ITEMID.localeCompare(b.ITEMID, undefined, { numeric: true });
    }),
    totals: totals,
    missingBwProducts: missingBwProducts.sort((a, b) => {
      if (a.STOREID !== b.STOREID) {
        return a.STOREID.localeCompare(b.STOREID, undefined, { numeric: true });
      }
      return a.ITEMID.localeCompare(b.ITEMID, undefined, { numeric: true });
    })
  };
};

const closeDiscrepancyModal = () => {
  showDiscrepancyModal.value = false;
};

const showResetModal = ref(false);

const toggleResetModal = () => {
  showResetModal.value = true;
};

const ResetModalHandler = () => {
  showResetModal.value = false;
};

const { user } = toRefs(props.auth);
const userRole = ref(user.value.role);
const isAdmin = computed(() => userRole.value === 'ADMIN');

const SYNCFG = () => {
  const userConfirmed = window.confirm('Reset Stocks');

  if (userConfirmed) {
    window.location.href = '/getcurrentstocks';
  } else {
    console.log('User cancelled the post operation.');
  }
};

const FIXED = () => {
  const userConfirmed = window.confirm('AUTO POST');

  if (userConfirmed) {
    window.location.href = '/autopost';
  } else {
    console.log('User cancelled the post operation.');
  }
};

// Variables for date display
const startDate = ref('');
const endDate = ref('');

// Initialize data when component mounts
onMounted(() => {
  // Prefetch TXT file data in the background
  fetchTxtFiles().catch(err => console.error('Failed to prefetch TXT file data:', err));
});

// Function to export discrepancy data to Excel
const exportDiscrepancyToExcel = async (discrepancyData) => {
  try {
    if ((!discrepancyData || !Array.isArray(discrepancyData) || discrepancyData.length === 0) && 
        (!missingBwProducts.value || missingBwProducts.value.length === 0)) {
      console.error('No discrepancy data to export');
      alert('No discrepancy data to export.');
      return;
    }

    const workbook = new ExcelJS.Workbook();
    const worksheet = workbook.addWorksheet('Discrepancy Report');

    // Define columns for the discrepancy report
    const columns = [
      { header: 'Date', key: 'DATE', width: 15 },
      { header: 'Store ID', key: 'STOREID', width: 12 },
      { header: 'Store Name', key: 'STORENAME', width: 20 },
      { header: 'Item ID', key: 'ITEMID', width: 15 },
      { header: 'Item Name', key: 'ITEMNAME', width: 30 },
      { header: 'System Count', key: 'systemCount', width: 15 },
      { header: 'TXT File Count', key: 'fileCount', width: 15 },
      { header: 'Difference', key: 'difference', width: 15 },
      { header: 'Status', key: 'status', width: 15 }
    ];

    worksheet.columns = columns;

    // Add header row with styling
    const headerRow = worksheet.getRow(1);
    headerRow.font = { bold: true };
    headerRow.fill = {
      type: 'pattern',
      pattern: 'solid',
      fgColor: { argb: '4F81BD' }
    };
    headerRow.alignment = { horizontal: 'center' };
    headerRow.font = { bold: true, color: { argb: 'FFFFFF' } };

    // Add data rows
    discrepancyData.forEach(item => {
      const row = worksheet.addRow({
        DATE: item.DATE,
        STOREID: item.STOREID,
        STORENAME: item.STORENAME,
        ITEMID: item.ITEMID,
        ITEMNAME: item.ITEMNAME,
        systemCount: item.systemCount,
        fileCount: item.fileCount,
        difference: item.difference,
        status: item.status
      });

      // Style difference cell based on value
      const differenceCell = row.getCell(8);
      if (item.difference > 0) {
        differenceCell.font = { color: { argb: 'FF0000' } }; // Red for positive difference
      } else if (item.difference < 0) {
        differenceCell.font = { color: { argb: '0000FF' } }; // Blue for negative difference
      }

      // Style status cell
      const statusCell = row.getCell(9);
      if (item.status === 'Discrepancy' || item.status === 'Missing in System') {
        statusCell.font = { color: { argb: 'FF0000' } }; // Red for discrepancy
      } else if (item.status === 'Missing in TXT File') {
        statusCell.font = { color: { argb: 'FF9900' } }; // Orange for missing in TXT
      } else {
        statusCell.font = { color: { argb: '008000' } }; // Green for match
      }
    });

    // Add summary row
    const totalDiscrepancies = discrepancyData.length;
    const positiveDiscrepancies = discrepancyData.filter(item => item.difference > 0).length;
    const negativeDiscrepancies = discrepancyData.filter(item => item.difference < 0).length;

    worksheet.addRow({});
    const summaryRow1 = worksheet.addRow(['Summary', '', '', '', '', '', '', '', '']);
    summaryRow1.font = { bold: true };
    worksheet.addRow(['Total Discrepancies', totalDiscrepancies, '', '', '', '', '', '', '']);
    worksheet.addRow(['System > TXT File', positiveDiscrepancies, '', '', '', '', '', '', '']);
    worksheet.addRow(['System < TXT File', negativeDiscrepancies, '', '', '', '', '', '', '']);
    
    // Add the total comparison
    worksheet.addRow({});
    const totalRow = worksheet.addRow(['Totals Comparison', '', '', '', '', '', '', '', '']);
    totalRow.font = { bold: true };
    const systemTotalRow = worksheet.addRow(['Total System Orders', discrepancyTotals.value.systemTotal, '', '', '', '', '', '', '']);
    const txtTotalRow = worksheet.addRow(['Total TXT File Orders', discrepancyTotals.value.txtFileTotal, '', '', '', '', '', '', '']);
    const diffRow = worksheet.addRow(['Difference (System - TXT)', discrepancyTotals.value.systemTotal - discrepancyTotals.value.txtFileTotal, '', '', '', '', '', '', '']);
    
    // Style the difference cell based on the value
    const totalDiffCell = diffRow.getCell(2);
    const totalDiff = discrepancyTotals.value.systemTotal - discrepancyTotals.value.txtFileTotal;
    if (totalDiff > 0) {
      totalDiffCell.font = { color: { argb: 'FF0000' } }; // Red for positive difference
    } else if (totalDiff < 0) {
      totalDiffCell.font = { color: { argb: '0000FF' } }; // Blue for negative difference
    }

    // Add Missing BW Products sheet if there are any
    if (missingBwProducts.value && missingBwProducts.value.length > 0) {
      const bwWorksheet = workbook.addWorksheet('Missing BW Products');
      
      // Define columns for BW products
      const bwColumns = [
        { header: 'Date', key: 'DATE', width: 15 },
        { header: 'Store ID', key: 'STOREID', width: 12 },
        { header: 'Store Name', key: 'STORENAME', width: 20 },
        { header: 'Item ID', key: 'ITEMID', width: 15 },
        { header: 'Item Name', key: 'ITEMNAME', width: 30 },
        { header: 'System Count', key: 'systemCount', width: 15 }
      ];
      
      bwWorksheet.columns = bwColumns;
      
      // Add header row with styling
      const bwHeaderRow = bwWorksheet.getRow(1);
      bwHeaderRow.font = { bold: true };
      bwHeaderRow.fill = {
        type: 'pattern',
        pattern: 'solid',
        fgColor: { argb: 'FF0000' } // Red for BW products
      };
      bwHeaderRow.alignment = { horizontal: 'center' };
      bwHeaderRow.font = { bold: true, color: { argb: 'FFFFFF' } };
      
      // Add BW product rows
      missingBwProducts.value.forEach(item => {
        bwWorksheet.addRow({
          DATE: item.DATE,
          STOREID: item.STOREID,
          STORENAME: item.STORENAME,
          ITEMID: item.ITEMID,
          ITEMNAME: item.ITEMNAME,
          systemCount: item.systemCount
        });
      });
      
      // Add summary
      bwWorksheet.addRow({});
      const bwSummaryRow = bwWorksheet.addRow(['Total Missing BW Products', missingBwProducts.value.length, '', '', '', '']);
      bwSummaryRow.font = { bold: true };
      
      // Add warning note
      bwWorksheet.addRow({});
      const warningRow = bwWorksheet.addRow(['WARNING: These BW products exist in the system but are missing in TXT files.', '', '', '', '', '']);
      warningRow.font = { bold: true, color: { argb: 'FF0000' } };
    }

    // Add current date information
    worksheet.addRow({});
    worksheet.addRow(['Report Date', new Date().toLocaleDateString('en-US', { 
      year: 'numeric', 
      month: 'long', 
      day: 'numeric' 
    }), '', '', '', '', '', '', '']);

    // Generate filename with date
    const now = new Date();
    const dateStr = `${now.getFullYear()}${String(now.getMonth() + 1).padStart(2, '0')}${String(now.getDate()).padStart(2, '0')}`;
    const filename = `Discrepancy_Report_${dateStr}.xlsx`;

    // Create and download file
    const buffer = await workbook.xlsx.writeBuffer();
    const blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = filename;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  } catch (error) {
    console.error('Error exporting discrepancy to Excel:', error);
    alert('Failed to export discrepancy report to Excel.');
  }
};

// Enhanced exportToExcel function with proper BW product sorting
const exportToExcel = async () => {
  try {
    if (!flattenedOrders.value || !Array.isArray(flattenedOrders.value) || flattenedOrders.value.length === 0) {
      console.error('No data to export');
      alert('No data to export.');
      return;
    }

    const workbook = new ExcelJS.Workbook();
    const worksheet = workbook.addWorksheet('Orders');

    const columns = [
      { header: 'ITEMID', key: 'ITEMID', width: 20 },
      { header: 'ITEMNAME', key: 'ITEMNAME', width: 25 },
      { header: 'CATEGORY', key: 'CATEGORY', width: 20 },
      { header: 'STOCKS(SYNC)', key: 'stocks', width: 15 },
      { header: 'REMAINING STOCKS', key: 'BalanceCount', width: 20 },
      { header: 'TOTAL', key: 'TOTAL', width: 15 }
    ];

    // Get all store names from the order data
    const storeNameSet = new Set();
    flattenedOrders.value.forEach(order => {
      if (order && typeof order === 'object') {
        Object.keys(order).forEach(key => {
          if (typeof order[key] === 'object' && order[key] !== null && 'count' in order[key]) {
            storeNameSet.add(key);
          }
        });
      }
    });

    // Sort store names by their numeric ID in the format "ID - NAME"
    const sortedStoreNames = Array.from(storeNameSet).sort((a, b) => {
      const aId = a.split(' - ')[0];
      const bId = b.split(' - ')[0];
      
      if (!isNaN(aId) && !isNaN(bId)) {
        return parseInt(aId) - parseInt(bId);
      }
      
      return aId.localeCompare(bId, undefined, {numeric: true, sensitivity: 'base'});
    });

    // Add store columns
    sortedStoreNames.forEach(storeName => {
      columns.push({ header: storeName, key: storeName, width: 25 });
    });

    worksheet.columns = columns;

    // Style the header row
    const headerRow = worksheet.getRow(1);
    headerRow.font = { bold: true };
    headerRow.fill = {
      type: 'pattern',
      pattern: 'solid',
      fgColor: { argb: '4F81BD' }
    };
    headerRow.alignment = { horizontal: 'center' };
    headerRow.font = { bold: true, color: { argb: 'FFFFFF' } };

    // Sort the orders before adding them to the worksheet
    // This is the key change for BW product sorting
    const sortedOrders = [...flattenedOrders.value].sort((a, b) => {
      const aId = a.ITEMID || '';
      const bId = b.ITEMID || '';
      
      // Special handling for BW products to ensure they sort correctly
      if (aId.startsWith('BW') && bId.startsWith('BW')) {
        // Extract the numeric portion
        const aNum = parseInt(aId.replace('BW', ''));
        const bNum = parseInt(bId.replace('BW', ''));
        
        // If both can be parsed as numbers, sort numerically
        if (!isNaN(aNum) && !isNaN(bNum)) {
          return aNum - bNum;
        }
      }
      
      // Default to standard string comparison
      return aId.localeCompare(bId, undefined, {numeric: true, sensitivity: 'base'});
    });

    // Add the sorted data rows
    sortedOrders.forEach(order => {
      if (order && typeof order === 'object') {
        const row = {
          ITEMID: order.ITEMID || '',
          ITEMNAME: order.ITEMNAME || '',
          CATEGORY: order.CATEGORY || '',
          stocks: order.stocks || 0,
          BalanceCount: order.BalanceCount || 0,
          TOTAL: order.TOTAL || 0
        };
        
        // Add store-specific data
        sortedStoreNames.forEach(storeName => {
          if (typeof order[storeName] === 'object' && order[storeName] !== null && 'count' in order[storeName]) {
            row[storeName] = order[storeName].count || 0;
          } else {
            row[storeName] = 0;
          }
        });
        
        worksheet.addRow(row);
      }
    });

    // Add totals row
    worksheet.addRow({});
    const totalsRow = worksheet.addRow({
      ITEMID: 'Grand Total',
      ITEMNAME: '',
      CATEGORY: '',
      stocks: columnTotals.value.stocks || 0,
      BalanceCount: columnTotals.value.BalanceCount || 0,
      TOTAL: columnTotals.value.TOTAL || 0
    });
    totalsRow.font = { bold: true };
    totalsRow.fill = {
      type: 'pattern',
      pattern: 'solid',
      fgColor: { argb: 'E2EFDA' }
    };

    // Add store totals to the total row
    sortedStoreNames.forEach(storeName => {
      totalsRow.getCell(storeName).value = columnTotals.value[storeName] || 0;
    });

    // Add current date information
    worksheet.addRow({});
    worksheet.addRow(['Report Date', new Date().toLocaleDateString('en-US', { 
      year: 'numeric', 
      month: 'long', 
      day: 'numeric' 
    }), '', '', '', '']);

    // Generate filename with date
    const today = new Date();
    const filename = `BW_Orders_${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}.xlsx`;
    
    // Create Excel file buffer
    const buffer = await workbook.xlsx.writeBuffer();
    
    // Trigger download
    const blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = filename;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  } catch (error) {
    console.error('Error exporting to Excel:', error);
    alert('Failed to export to Excel: ' + error.message);
  }
};
</script>