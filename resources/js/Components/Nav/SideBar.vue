<script setup>
import Home from "@/Components/Svgs/Home.vue";
import Order from "@/Components/Svgs/Order.vue";
import Transactions from "@/Components/Svgs/Transactions.vue";
import Wallet from "@/Components/Svgs/Wallet.vue";
import Promo from "@/Components/Svgs/Promo.vue";
import Dashboard from "@/Components/Svgs/Dashboard.vue";
import Setting from "@/Components/Svgs/Setting.vue";
import Logout from "@/Components/Svgs/Logout.vue";
import Truck from "@/Components/Svgs/Truck.vue";
import POS from "@/Components/Svgs/Pos.vue";
import Reports from "@/Components/Svgs/Reports.vue";
import Stock from "@/Components/Svgs/Stock.vue";
import Register from "@/Components/Svgs/Register.vue";

import List from "@/Components/Nav/List.vue";

const props = defineProps({
    isSidebarOpen: {
        type: Boolean,
        default: true
    },
    activeTab: {
        type: String,
        default: "HOME"
    }
});

const emit = defineEmits();

const logout = () => {
    emit('logout');
};
</script>

<template>
    <div :class="[
            'rounded-tr-3xl rounded-br-3xl p-4 bg-navy top-0 bottom-0 left-0 text-black font-bold absolute z-20 pt-12 pb-2 flex flex-col justify-between transition-all duration-500 ease-in-out px-2 lg:w-28',
            (isSidebarOpen ? 'translate-x-0' : 'translate-x-[-100%]')
        ]">
        <!-- <ul class="flex justify-center flex-col items-center gap-3 text-xs max-h-96 overflow-y-auto">
            <li class="flex justify-center flex-col items-center mb-5 p-3 text-white text-lg font-sans font-extrabold rounded-md w-full max-w-5x"> -->
        <ul class="flex justify-center flex-col items-center gap-3 text-xs">
            <li class="flex justify-center flex-col items-center mb-5 p-3 text-white text-lg font-sans font-extrabold rounded-md w-full">
                ECPOS
            </li>

            <div class="tooltip tooltip-right tooltip-primary" data-tip="Master">
                <List :active-tab="activeTab" tabName="DASHBOARD" url="/admin" ><Dashboard class="h-6 lg:h-8"/> </List>
            </div>

            <div class="tooltip tooltip-right tooltip-primary" data-tip="POS">
                <List :active-tab="activeTab" tabName="POS" url="/dashboard"><POS class="h-6 lg:h-8"/> </List>
            </div>

            <div class="tooltip tooltip-right tooltip-primary" data-tip="Delivery">
                <List :active-tab="activeTab" tabName="DELIVERY" ><Truck class="h-6 lg:h-8"/> </List>
            </div>

            <div class="tooltip tooltip-right tooltip-primary" data-tip="Stock Counting">
                <List :active-tab="activeTab" tabName="STOCK" ><Stock class="h-6 lg:h-8"/> </List>
            </div>

            <div class="tooltip tooltip-right tooltip-primary" data-tip="Reports">
                <List :active-tab="activeTab" tabName="REPORTS" ><Reports class="h-6 lg:h-8"/> </List>
            </div>

            <div class="tooltip tooltip-right tooltip-primary" data-tip="Settings">
                <List :active-tab="activeTab" tabName="REGISTER" ><Register class="h-6 lg:h-8"/></List>
            </div>

            <div class="tooltip tooltip-right tooltip-primary" data-tip="Logout">
                <List :active-tab="activeTab" tabName="SETTINGS">
                    <button @click="logout"><Logout class="h-6 lg:h-8"/></button>
                </List>
            </div>


        </ul>
        
    </div>
</template>

