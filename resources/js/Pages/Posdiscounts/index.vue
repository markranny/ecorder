<script setup>
import Create from "@/Components/POSdiscount/Create.vue";
import Update from "@/Components/POSdiscount/Update.vue";
import Delete from "@/Components/POSdiscount/Delete.vue";
import PrimaryButton from "@/Components/Buttons/PrimaryButton.vue";
import DangerButton from "@/Components/Buttons/DangerButton.vue";
import TableContainer from "@/Components/Tables/TableContainer.vue";
import Main from "@/Layouts/RetailPanel.vue";
import Add from "@/Components/Svgs/Add.vue";
import Date from "@/Components/Svgs/Date.vue";
import { ref } from "vue";

import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net';
DataTable.use(DataTablesCore);

const OFFERID  = ref('');
const DESCRIPTION = ref('');
const STATUS = ref('');
const PDTYPE = ref('');
const PRIORITY = ref('');
const DISCVALIDPERIODID = ref('');
const DISCOUNTTYPE = ref('');
const NOOFLINESTOTRIGGER = ref('');
const DEALPRICEVALUE = ref('');
const DISCOUNTPCTVALUE = ref('');
const DISCOUNTAMOUNTVALUE = ref('');
const PRICEGROUP = ref('');
const showModalUpdate = ref(false);
const showCreateModal = ref(false);
const showDeleteModal = ref(false);

const props = defineProps({
    posperiodicdiscounts: {
        type: Array,
        required: true,
    },
    DISCVALIDPERIODID: {
        type: Array,
        required: true,
    },
});

const columns = [
    { data: 'OFFERID', title: 'OFFERID' },
    { data: 'DISCVALIDPERIODID', title: 'DISCVALIDPERIODID' },
    { data: 'DESCRIPTION', title: 'DESCRIPTION' },
    { data: 'STATUS', title: 'STATUS' },
    /* { data: 'PDTYPE', title: 'PDTYPE' }, */
    /* { data: 'PRIORITY', title: 'PRIORITY' }, */
    { data: 'DISCOUNTTYPE', title: 'DISCOUNTTYPE' },
    /* { data: 'NOOFLINESTOTRIGGER', title: 'NOOFLINESTOTRIGGER' }, */
    { data: 'DEALPRICEVALUE', title: 'DEALPRICEVALUE' },
    { data: 'DISCOUNTPCTVALUE', title: 'DISCOUNTPCTVALUE' },
    { data: 'DISCOUNTAMOUNTVALUE', title: 'DISCOUNTAMOUNTVALUE' },
    /* { data: 'PRICEGROUP', title: 'PRICEGROUP' }, */
    {
        data: null,
        render: '#action',
        title: 'ACTIONS'
    },
];

const options = {
    paging: false,
    scrollX: true,
    scrollY: "70vh",
    scrollCollapse: true,
};


const toggleUpdateModal = (newOFFERID, newDESCRIPTION, newSTATUS, newDISCOUNTTYPE, newDISCVALIDPERIODID) => {
    OFFERID.value = newOFFERID;
    DESCRIPTION.value = newDESCRIPTION;
    STATUS.value = newSTATUS;
    DISCOUNTTYPE.value = newDISCOUNTTYPE;
    DISCVALIDPERIODID.value = newDISCVALIDPERIODID;
    showModalUpdate.value = true;
};
const toggleDeleteModal = (newOFFERID) => {
    OFFERID.value = newOFFERID;
    showDeleteModal.value = true;
};

/* const toggleCreateModal = () => {
    showCreateModal.value = true;
}; */

const toggleCreateModal = (discvalidperiodid) => {
    if (discvalidperiodid && typeof discvalidperiodid === 'object' && 'discvalidperiodid' in discvalidperiodid) {
        DISCVALIDPERIODID.value = discvalidperiodid.discvalidperiodid;
    } else if (discvalidperiodid) {
        DISCVALIDPERIODID.value = discvalidperiodid;
    } else {
        DISCVALIDPERIODID.value = null;
    }

    showCreateModal.value = true;
    console.log(DISCVALIDPERIODID.value);
};

const updateModalHandler = () => {
    showModalUpdate.value = false;
};
const createModalHandler = () => {
    showCreateModal.value = false;
};
const deleteModalHandler = () => {
    showDeleteModal.value = false;
};

const navigateToPosDiscount = (offerid, DISCOUNTTYPE) => {
  /* console.log('Redirecting to Mix And Match Entries for account:', offerid / DISCOUNTTYPE); */
  /* window.alert('You are Redirecting to Discount Validation Period Entries', offerid / DISCOUNTTYPE); */
  window.location.href = `/MNM/${offerid}/${DISCOUNTTYPE}`;
};

const posdiscvalidationperiods = () => {
  window.alert('You are Redirecting to Discount Validation Period Entries');
  window.location.href = '/posdiscvalidationperiods';
};

</script>

<template>
    <Main active-tab="RETAILITEMS">
        <template v-slot:modals>
            <Create :show-modal="showCreateModal" :DISCVALIDPERIODID="DISCVALIDPERIODID" @toggle-active="createModalHandler"  />
            <Update :show-modal="showModalUpdate" :OFFERID="OFFERID" :DESCRIPTION="DESCRIPTION" :STATUS="STATUS" :DISCOUNTTYPE="DISCOUNTTYPE"  :DISCVALIDPERIODID="DISCVALIDPERIODID" @toggle-active="updateModalHandler"  />
            <Delete :show-modal="showDeleteModal" item-name="posperiodicdiscounts" :OFFERID="OFFERID" @toggle-active="deleteModalHandler"  />
        </template>

        <template v-slot:main>
            <!-- <PrimaryButton type="button" @click="toggleCreateModal" >CREATE</PrimaryButton> -->
            <TableContainer>
                <div class="absolute adjust">
                    <div class="flex justify-start items-center">

                        <PrimaryButton
                        type="button"
                        @click="toggleCreateModal({ discvalidperiodid: props.DISCVALIDPERIODID })"
                        class="m-6 bg-navy"
                        >
                        <Add class="h-4" />
                        </PrimaryButton>

                        <PrimaryButton
                        type="button"
                        @click="posdiscvalidationperiods('/posdiscvalidationperiods')"
                        class="m-6 bg-navy"
                        >
                        <Date class="h-4" />
                        </PrimaryButton>

                    </div>
                </div>
                <DataTable :data="posperiodicdiscounts" :columns="columns" class="w-full relative display" :options="options" >
                    <template #action="data">
                        <PrimaryButton type="button" @click="toggleUpdateModal(data.cellData.OFFERID, data.cellData.DESCRIPTION, data.cellData.STATUS, data.cellData.DISCOUNTTYPE, data.cellData.DISCVALIDPERIODID, { discvalidperiodid: props.DISCVALIDPERIODID } )" class="me-1">
                            Update
                        </PrimaryButton>
                        <!-- <DangerButton type="button" @click="toggleDeleteModal(data.cellData.OFFERID)">
                            Delete
                        </DangerButton> -->
                        <DangerButton type="button" @click="navigateToPosDiscount(data.cellData.OFFERID, data.cellData.DISCOUNTTYPE)">
                            MIX AND MATCH
                        </DangerButton>
                    </template>
                </DataTable>
            </TableContainer>
        </template>
    </Main>
</template>
