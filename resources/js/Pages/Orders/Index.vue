<script setup>
import { useRouter } from 'vue-router'
import Create from "@/Components/Orders/Create.vue";
import Update from "@/Components/Orders/Update.vue";
import Post from "@/Components/ItemOrders/Post.vue";
import SendModal from "@/Components/Orders/Send.vue";

import PrimaryButton from "@/Components/Buttons/PrimaryButton.vue";
import SuccessButton from "@/Components/Buttons/SuccessButton.vue";
import Main from "@/Layouts/AdminPanel.vue";
import Excel from "@/Components/Exports/Excel.vue";
import TxtFile from "@/Components/Svgs/TxtFile.vue";
import Send from "@/Components/Svgs/Send.vue";

import Add from "@/Components/Svgs/Add.vue";    
import editblue from "@/Components/Svgs/editblue.vue";
import moreblue from "@/Components/Svgs/moreblue.vue";
import PostIcon from "@/Components/Svgs/Post.vue";

import TableContainer from "@/Components/Tables/TableContainer.vue";
import { openDB, addOrder, getAllOrders } from '@/IndexDB/OrderDB';
import { ref, computed, onMounted, onUnmounted } from "vue";

import Swal from 'sweetalert2';

import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net';
DataTable.use(DataTablesCore);

const journalid = ref('');
const description = ref('');
const createddatetime = ref('');
const storeid = ref('');


const showModalUpdate = ref(false);
const showCreateModal = ref(false);
const showSendModal = ref(false);
const showModalMore = ref(false);
const showPostModal = ref(false);

const props = defineProps({
    inventjournaltables: {
        type: Array,
        required: true,
    },
    orders: {
        type: Array,
        required: true,
    },
});
const txtfilecolumns = [
    { data: 'STOREID', title: 'STOREID' },
    { 
        data: 'POSTEDDATETIME', 
        title: 'POSTEDDATETIME',
        render: function(data, type, row) {
            const date = new Date(data);
            return date.toLocaleDateString(); 
        }
    },
    { data: 'ITEMID', title: 'ITEMID' },
    { data: 'COUNTED', title: 'COUNTED' },
];

// Fixed DataTable columns configuration
const columns = [
    { data: 'posted', title: 'POSTED' },
    { data: 'sent', title: 'SENT' },
    { data: 'description', title: 'TR #' },
    { data: 'qty', title: 'QTY' },
    // Removed the commented out amount column that might be causing issues
    { 
        data: 'createddatetime', 
        title: 'DATE ORDER',
        render: function(data, type, row) {
            if (type === 'sort' || type === 'type') {
                return data; // Return raw data for sorting
            }
            if (!data) return ''; // Handle null values
            const date = new Date(data);
            if (isNaN(date.getTime())) return data; // Return original if invalid date
            
            const formattedDate = date.toLocaleDateString();
            const hours = date.getHours();
            const minutes = date.getMinutes().toString().padStart(2, '0');
            const ampm = hours >= 12 ? 'PM' : 'AM';
            const formattedHours = (hours % 12) || 12; // Convert 0 to 12 for 12 AM
            const formattedTime = `${formattedHours}:${minutes} ${ampm}`;
            return `${formattedDate} ${formattedTime}`;
        }
    },
    { 
        data: 'datecreated', 
        title: 'CREATEDDATE',
        render: function(data, type, row) {
            if (!data) return '';
            if (type === 'sort' || type === 'type') {
                return data; // Return raw data for sorting
            }
            const date = new Date(data);
            if (isNaN(date.getTime())) return data; // Return original if invalid date
            
            const formattedDate = date.toLocaleDateString();
            const hours = date.getHours();
            const minutes = date.getMinutes().toString().padStart(2, '0');
            const ampm = hours >= 12 ? 'PM' : 'AM';
            const formattedHours = (hours % 12) || 12; // Convert 0 to 12 for 12 AM
            const formattedTime = `${formattedHours}:${minutes} ${ampm}`;
            return `${formattedDate} ${formattedTime}`;
        }
    },
    {
        data: null,
        defaultContent: '', // Add default content
        render: '#action',
        title: 'ACTIONS'
    },
];

const options = {
    paging: false,
    scrollX: true,
    scrollY: "60vh",
    scrollCollapse: true,
    order: [[4, 'desc']], // Sort by DATE ORDER column (index 4) in descending order
    columnDefs: [
        {
            targets: [4], // DATE ORDER column
            className: 'dt-head-center dt-body-center' // Add styling for better visibility
        }
    ],
    // Add error handling
    drawCallback: function(settings) {
        console.log('Table drawn successfully');
    },
    // Make sure columns are properly initialized
    initComplete: function(settings, json) {
        console.log('DataTable initialization complete');
    }
};

const togglePostModal = (newJOURNALID,) => {
    journalid.value = newJOURNALID;
    showPostModal.value = true;
};


const toggleUpdateModal = (newJOURNALID, newDESCRIPTION) => {
    
    journalid.value = newJOURNALID;
    description.value = newDESCRIPTION;
    showModalUpdate.value = true;
};
const toggleSendModal = (newJOURNALID) => {
    journalid.value = newJOURNALID;
    showSendModal.value = true;
};

const toggleCreateModal = () => {
    showCreateModal.value = true;
};

const toggleMoreModal = (newJOURNALID) => {
    journalid.value = newJOURNALID;
    showModalMore.value = true;
};


const updateModalHandler = () => {
    showModalUpdate.value = false;
};
const createModalHandler = () => {
    showCreateModal.value = false;
};
const sendModalHandler = () => {
    showSendModal.value = false;  
};
const MoreModalHandler = () => {
    showModalMore.value = false;
};
const postModalHandler = () => {
    showPostModal.value = false;
};

const router = useRouter()

const navigateToOrder = (journalid) => {
  console.log('Redirecting to Item Order Entries for account:', journalid);
  window.location.href = `/ItemOrders/${journalid}`;
};

const currentDate = computed(() => {
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    return `${year}${month}${day}`;
});

function generateTextFileContent(orders, columns) {
    const dataRows = orders.map(order => {
        const rowData = columns.map(column => {
            if (column.data === 'POSTEDDATETIME') {
                const date = new Date(order[column.data]);
                return date.toLocaleDateString();
            } else if (column.data === 'COUNTED') {
                return Math.floor(order[column.data]) || ''; 
            } else {
                return order[column.data] || '';
            }
        });
        return rowData.join('|');
    });

    return dataRows.join('\n');
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

const postAndSend = async (journalId) => {
  try {
    // Ensure journalId is a string
    const journalIdString = String(journalId);
    
    console.log('Processing order for journal ID:', journalIdString);
    
    // Find the order in the props data to get its date
    const orderRecord = props.inventjournaltables.find(order => order.journalid === journalId);
    const orderDate = orderRecord?.createddatetime ? new Date(orderRecord.createddatetime).toLocaleDateString() : new Date().toLocaleDateString();
    
    // Show loading SweetAlert
    Swal.fire({
      title: 'Processing Order',
      text: 'Please wait while your order is being processed...',
      icon: 'info',
      allowOutsideClick: false,
      showConfirmButton: false,
      didOpen: () => {
        Swal.showLoading();
      }
    });
    
    // Get CSRF token from meta tag
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    if (!csrfToken) {
      console.error('CSRF token not found in page');
      throw new Error('CSRF token not found');
    }
    
    console.log('Using CSRF token:', csrfToken);
    
    // Define itemsData at a higher scope so it's accessible throughout the function
    let itemsData = {
      totalQty: 'N/A',
      hasItems: false,
      totalOrders: 0 // Add total orders to the data
    };
    
    // First, check if we have items to post
    console.log('Checking if order has items...');
    
    try {
      const itemsResponse = await fetch(`/check-order-has-items/${journalIdString}`);
      console.log('Items check response status:', itemsResponse.status);
      
      // Log the raw response for debugging
      const itemsResponseText = await itemsResponse.text();
      console.log('Raw items check response:', itemsResponseText);
      
      // Try to parse as JSON
      try {
        itemsData = JSON.parse(itemsResponseText);
        console.log('Parsed items data:', itemsData);
      } catch (parseError) {
        console.error('Error parsing items response as JSON:', parseError);
        throw new Error('Server returned invalid JSON when checking items');
      }
      
      if (!itemsData.hasItems) {
        Swal.fire({
          title: 'No Items Found',
          text: 'No items with non-zero counts found in this order.',
          icon: 'warning',
          confirmButtonText: 'OK'
        });
        return;
      }
      
    } catch (itemsError) {
      console.error('Error checking if order has items:', itemsError);
      Swal.fire({
        title: 'Error',
        text: 'Failed to check if order has items: ' + itemsError.message,
        icon: 'error',
        confirmButtonText: 'OK'
      });
      return;
    }
    
    // Get total quantity
    const totalQty = itemsData.totalQty || orderRecord?.qty || 'N/A';
    const totalOrders = itemsData.totalOrders || 0;
    
    // Confirm with user before proceeding
    const confirmResult = await Swal.fire({
      title: 'Confirm Order Posting',
      html: `Are you sure you want to post this order?<br><br>` +
            `<b style="color: red;">Total Quantity: ${totalQty}</b><br>` +
            `<b style="color: red;">Total Orders: ${totalOrders}</b><br>` +
            `<b style="color: red;">Date: ${orderDate}</b>`,
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Yes, Post Order',
      cancelButtonText: 'Cancel'
    });

    if (!confirmResult.isConfirmed) {
      console.log('User cancelled order posting');
      return;
    }

    // CRITICAL FIX: Add a client-side flag to prevent duplicate submissions
    // Check if this button was already clicked
    if (window.isPostingOrder) {
      console.log('Order posting already in progress, preventing duplicate submission');
      return;
    }
    
    // Set the flag to prevent duplicate clicks
    window.isPostingOrder = true;
    
    try {
      // Generate and download text files first
      console.log('Generating text files...');
      const filesGenerated = await generateAndDownloadTextFile(journalIdString);
      console.log('Files generated successfully:', filesGenerated);
      
      if (!filesGenerated) {
        console.warn('Text file generation failed or was cancelled');
        Swal.fire({
          title: 'Process Cancelled',
          text: 'Text file generation failed or was cancelled.',
          icon: 'warning',
          confirmButtonText: 'OK'
        });
        window.isPostingOrder = false; // Reset the flag
        return;
      }
      
      // Now post the order
      console.log('Posting order...');
      const postResponse = await fetch('/post-order', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ journalid: journalIdString })
      });
      
      console.log('Post response status:', postResponse.status);
      console.log('Post response headers:', Object.fromEntries([...postResponse.headers]));
      
      // Get the raw response text first
      const responseText = await postResponse.text();
      console.log('Raw post response:', responseText);
      
      // Try to parse as JSON
      let responseData;
      try {
        if (responseText.trim().startsWith('{') || responseText.trim().startsWith('[')) {
          responseData = JSON.parse(responseText);
          console.log('Parsed response data:', responseData);
        } else {
          console.error('Server returned non-JSON response:', responseText.substring(0, 200) + '...');
          throw new Error('Server returned HTML instead of JSON. Please check server logs.');
        }
      } catch (parseError) {
        console.error('Error parsing response as JSON:', parseError);
        throw new Error('Server returned invalid response: ' + responseText.substring(0, 100) + '...');
      }
      
      if (!postResponse.ok) {
        throw new Error(responseData.message || 'Error posting order');
      }
      
      // Check if the order was already posted earlier
      if (responseData.already_posted) {
        Swal.fire({
          title: 'Already Posted',
          text: 'This order has already been posted and sent.',
          icon: 'info',
          confirmButtonText: 'OK'
        }).then((result) => {
          // Reload the page to reflect changes
          if (result.isConfirmed) {
            location.reload();
          }
        });
        return;
      }
      
      // Success message with details
      Swal.fire({
        title: 'Success!',
        html: `Order has been successfully posted and sent!<br><br>` +
              `<b style="color: red;">Total Quantity: ${responseData.totalQty || totalQty}</b><br>` +
              `<b style="color: red;">Total Orders: ${totalOrders}</b><br>` +
              `<b style="color: red;">Date: ${orderDate}</b>`,
        icon: 'success',
        confirmButtonText: 'OK'
      }).then((result) => {
        // Reload the page to reflect changes
        if (result.isConfirmed) {
          location.reload();
        }
      });
    } finally {
      // Always reset the posting flag, even if there's an error
      window.isPostingOrder = false;
    }
  } catch (error) {
    console.error('Error in postAndSend:', error);
    Swal.fire({
      title: 'Error',
      text: 'Error posting and sending order: ' + error.message,
      icon: 'error',
      confirmButtonText: 'OK'
    });
    // Make sure to reset the flag even in case of errors
    window.isPostingOrder = false;
  }
};

// Updated generateAndDownloadTextFile function with correct folder paths
async function generateAndDownloadTextFile(journalId) {
    // Ensure journalId is a string
    const journalIdString = String(journalId);
    
    console.log('Generating text file for journal ID:', journalIdString);
    
    try {
        // Fetch orders for the specific journal ID
        const response = await fetch(`/api/get-orders-for-journal/${journalIdString}`);
        
        // Check if response is OK
        if (!response.ok) {
            const errorText = await response.text();
            console.error('Server response:', errorText);
            throw new Error(`Failed to fetch orders: ${response.status} ${response.statusText}`);
        }
        
        // Check content type
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            const responseText = await response.text();
            console.error('Received non-JSON response:', responseText);
            throw new Error('Server returned an invalid response format');
        }
        
        const journalOrders = await response.json();
        console.log('Retrieved orders:', journalOrders);
        
        if (!journalOrders || journalOrders.length === 0) {
            alert("No orders available for this transaction.");
            return false;
        }
        
        // Filter for non-zero counts first
        const nonZeroOrders = journalOrders.filter(order => 
            order.COUNTED && Number(order.COUNTED) > 0
        );
        
        if (nonZeroOrders.length === 0) {
            alert("No items with non-zero counts to report for this transaction.");
            return false;
        }

        const storeID = nonZeroOrders[0].STOREID;
        if (!storeID) {
            alert("Store ID is missing from the order data.");
            return false;
        }

        // Get current date if POSTEDDATETIME is not available
        let postedDate;
        if (nonZeroOrders[0].POSTEDDATETIME) {
            postedDate = new Date(nonZeroOrders[0].POSTEDDATETIME);
        } else {
            postedDate = new Date();
        }
        
        if (isNaN(postedDate.getTime())) {
            alert("Invalid posted date in the order data.");
            return false;
        }

        // Format the date for folder name
        const formattedPostedDate = `${postedDate.getFullYear()}${(postedDate.getMonth() + 1).toString().padStart(2, '0')}${postedDate.getDate().toString().padStart(2, '0')}`;
        const mainFolderName = formattedPostedDate;
        const header = `${storeID}|${postedDate.toLocaleDateString()}`;

        // Separate orders by department
        const nonProductOrders2 = nonZeroOrders.filter(order => 
            order.WAREHOUSEDEPARTMENT === 'TRADING'
        );
        const nonProductOrders = nonZeroOrders.filter(order => 
            order.WAREHOUSEDEPARTMENT === 'SR'
        );
        const regularProductOrders = nonZeroOrders.filter(order => 
            !order.WAREHOUSEDEPARTMENT || order.WAREHOUSEDEPARTMENT === 'NONE'
        );

        // Format rows for each category
        const nonProductRows2 = nonProductOrders2.map(order => 
            `${order.ITEMID}|${Math.floor(order.COUNTED)}`
        );
        const nonProductRows = nonProductOrders.map(order => 
            `${order.ITEMID}|${Math.floor(order.COUNTED)}`
        );
        const regularProductRows = regularProductOrders.map(order => 
            `${order.ITEMID}|${Math.floor(order.COUNTED)}`
        );

        // Double check if we have any items to report after formatting
        if (nonProductRows2.length === 0 && nonProductRows.length === 0 && regularProductRows.length === 0) {
            alert("No items with non-zero counts to report for this transaction.");
            return false;
        }

        const filename = `${storeID}${formattedPostedDate}.txt`;
        const saveFiles = [];

        // Save REGULAR PRODUCT file if there are items
        if (regularProductRows.length > 0) {
            const regularProductContent = [header, ...regularProductRows].join('\n');
            saveFiles.push({
                content: regularProductContent,
                filename: filename,
                folderName: mainFolderName,
                journalId: journalIdString
            });
        }

        // Save NON PRODUCT file if there are items - to SR/{date} folder
        if (nonProductRows.length > 0) {
            const nonProductContent = [header, ...nonProductRows].join('\n');
            saveFiles.push({
                content: nonProductContent,
                filename: filename,
                folderName: `SR/${mainFolderName}`,
                journalId: journalIdString
            });
        }

        // Save NON PRODUCT 2 file if there are items - to SO/{date} folder
        if (nonProductRows2.length > 0) {
            const nonProductContent2 = [header, ...nonProductRows2].join('\n');
            saveFiles.push({
                content: nonProductContent2,
                filename: filename,
                folderName: `SO/${mainFolderName}`,
                journalId: journalIdString
            });
        }

        console.log('Prepared files for saving:', saveFiles.length);

        // Save all files that have content
        for (const file of saveFiles) {
            console.log('Saving file:', file.folderName + '/' + file.filename);
            
            const saveResponse = await fetch('/save-file', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    content: file.content,
                    filename: file.filename,
                    folderName: file.folderName,
                    journalId: file.journalId
                })
            });

            if (!saveResponse.ok) {
                const errorText = await saveResponse.text();
                console.error('Save file response:', errorText);
                throw new Error(`Failed to save file ${file.folderName}/${file.filename}: ${saveResponse.status} ${saveResponse.statusText}`);
            }

            // Check if response is JSON
            const saveContentType = saveResponse.headers.get('content-type');
            if (!saveContentType || !saveContentType.includes('application/json')) {
                const responseText = await saveResponse.text();
                console.error('Received non-JSON response for save file:', responseText);
                throw new Error('Server returned an invalid response format for file save');
            }

            const data = await saveResponse.json();
            if (!data.success) {
                throw new Error('Error saving file: ' + (data.message || 'Unknown error'));
            }
        }

        console.log('Files saved successfully:', saveFiles.map(f => `${f.folderName}/${f.filename}`));
        return true; // Return success indication

    } catch (error) {
        console.error('Error in generateAndDownloadTextFile:', error);
        alert('Error saving files | CUTOFF : ' + error.message);
        return false; // Return failure indication
    }
}

function saveFileToPublicDirectory(filePath, content) {
    const url = `${window.location.origin}/${filePath}`;
    const link = document.createElement('a');
    link.href = url;
    link.download = filePath.split('/').pop();
    link.style.display = 'none';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

const showWarning = ref(false);
const currentTime = ref('');

let audio;

// Modified to remove time restrictions
const startWarningSound = () => {
  console.log('Starting warning sound');
  if (!audio) {
    audio = new Audio('/audio/warning.ogg');
    audio.loop = true;
  }
  audio.play().then(() => {
    console.log('Audio started playing');
  }).catch(error => {
    console.error('Error playing audio:', error);
  });
};

const stopWarningSound = () => {
  if (audio) {
    audio.pause();
    audio.currentTime = 0;
  }
};

// Modified to remove time restrictions
const checkTime = () => {
  const now = new Date();
  const hours = now.getHours();
  const minutes = now.getMinutes();
  
  console.log(`Current time: ${hours}:${minutes}`);
  
  // We're removing the time-based warning that was blocking functionality
  // If you still want to show warnings at certain times without blocking functionality:
  if ((hours === 13 && minutes === 32) || (hours === 16 && minutes === 40)) {
    console.log('Warning time reached but not showing modal');
    // You can optionally log this or show a non-blocking notification
    // But we're not showing the blocking modal or playing sounds
  }
};

const closeWarning = () => {
  showWarning.value = false;
  stopWarningSound();
};

let interval;

onMounted(() => {
  audio = new Audio('/audio/warning.ogg');
  audio.loop = true;
  interval = setInterval(checkTime, 60000);
  checkTime();
});

onUnmounted(() => {
  clearInterval(interval);
  stopWarningSound();
});
</script>

<template>
    <!-- Warning modal - kept but set to never show by default -->
    <div v-if="showWarning" class="fixed inset-0 flex items-center justify-center z-50">
      <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-lg">
        <p class="font-bold">Warning</p>
        <p>It's {{ currentTime }}!</p>
        <button @click="closeWarning" class="mt-2 bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded">
          Close
        </button>
      </div>
    </div>
    <Main active-tab="ORDER">
        <template v-slot:modals>
            <Create :show-modal="showCreateModal" @toggle-active="createModalHandler"  />
            <Update :show-modal="showModalUpdate"  :journalid="journalid" :description="description"  @toggle-active="updateModalHandler"  />
            <SendModal :show-modal="showSendModal" item-name="inventjournaltables" :journalid="journalid" @toggle-active="sendModalHandler"  />
            <Post :show-modal="showPostModal" item-name="inventjournaltrans" :journalid="journalid" @toggle-active="postModalHandler"  />


            <More
            :show-modal="showModalMore"
            :accountnum="accountnum"
            @toggle-active="MoreModalHandler"
            />
        </template>

        <template v-slot:main>

            <TableContainer>
                
                <div class="absolute adjust">
                    <div class="flex justify-start items-center">

                        <PrimaryButton
                        type="button"
                        @click="toggleCreateModal"
                        class="m-6 bg-navy"
                        >
                        <Add class="h-4" />
                        </PrimaryButton>

                        <PrimaryButton
                                class="ml-5 cursor-pointer bg-navy hidden"
                                @click="generateAndDownloadTextFile"
                                >
                                <Send class="h-4"></Send>
                        </PrimaryButton>

                        <button @click="startWarningSound" hidden>Start Warning Sound</button>

                    </div>
                </div>

                <DataTable :data="inventjournaltables" :columns="columns" class="w-full relative display" :options="options" >
                    <template #action="data">

                        <div class="flex justify-start">
                                <PrimaryButton
                                class="cursor-pointer bg-navy"
                                @click="navigateToOrder(data.cellData.journalid)"
                                >
                                 ORDER
                                </PrimaryButton>

                                <PrimaryButton
                                    class="ml-5 cursor-pointer bg-red-900"
                                    @click="postAndSend(data.cellData.journalid)"
                                >
                                    POST & SEND
                                </PrimaryButton>

                            </div>

                    </template>
                </DataTable>

            </TableContainer>



                <TableContainer class="hidden">
                    <div class="absolute adjust">

                        <div class="flex justify-start items-center">
                        

                            &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<button @click="generateAndDownloadTextFile">Generate and Download Text File</button>
                                

                            <SuccessButton
                                type="button"
                                @click="exportToExcel"
                                class="m-6 bg-green"
                                >
                                <ExcelIcon class="h-4" />
                            </SuccessButton>

                            
                        </div>
                    </div>
                
                    <DataTable :data="orders" :columns="txtfilecolumns" class="w-full relative display" :options="options">
                        <template #action="data">
                            <div class="flex justify-start">
                            </div>
                        </template>
                    </DataTable>
                </TableContainer>
        </template>
    </Main>

</template>