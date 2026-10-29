<?php

namespace Database\Seeders;

use App\Models\Equipment;
use Illuminate\Database\Seeder;

class EquipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $equipments = [
            [
                'id' => 1,
                'category_id' => 1,
                'asset_code' => 'EQ-SRV-001',
                'name' => 'Dell PowerEdge T40 Server',
                'specification' => 'Intel Xeon E-2224G 3.5GHz, 16GB DDR4 ECC RAM, 1TB NVMe SSD + 2TB SATA HDD, Gigabit NIC, Chassis Mini-Tower',
                'condition' => 'baik',
                'status' => 'dipinjam',
                'daily_rental_fee' => 75000,
                'image_path' => 'equipments/eq_srv_001.png',
            ],
            [
                'id' => 2,
                'category_id' => 1,
                'asset_code' => 'EQ-SRV-002',
                'name' => 'HP ProLiant MicroServer Gen10',
                'specification' => 'AMD Opteron X3421 4-Core @2.1GHz, 16GB DDR4, 4x Drive Bays, Dual Port 1GbE, ClearOS & Ubuntu Ready',
                'condition' => 'baik',
                'status' => 'tersedia',
                'daily_rental_fee' => 60000,
                'image_path' => 'equipments/eq_srv_002.png',
            ],
            [
                'id' => 3,
                'category_id' => 2,
                'asset_code' => 'EQ-IOT-001',
                'name' => 'ESP32 IoT Development Kit Pro',
                'specification' => 'ESP-WROOM-32 240MHz Dual Core, Wi-Fi 802.11 b/g/n, Bluetooth 4.2 BLE, 0.96 inch I2C OLED, Multiple Sensor Modules',
                'condition' => 'baik',
                'status' => 'tersedia',
                'daily_rental_fee' => 12000,
                'image_path' => 'equipments/eq_iot_001.png',
            ],
            [
                'id' => 4,
                'category_id' => 2,
                'asset_code' => 'EQ-IOT-002',
                'name' => 'Arduino Mega 2560 Smart Agriculture Kit',
                'specification' => 'Microchip ATmega2560 16MHz, Capacitive Soil Moisture, DHT22 Temp/Humidity, 4-Channel Relay, 12V DC Pump, LCD 20x4 I2C',
                'condition' => 'rusak_ringan',
                'status' => 'dalam_perbaikan',
                'daily_rental_fee' => 10000,
                'image_path' => 'equipments/eq_iot_002.png',
            ],
            [
                'id' => 5,
                'category_id' => 3,
                'asset_code' => 'EQ-DRN-001',
                'name' => 'DJI Mavic Air 2 Fly More Combo',
                'specification' => 'Kamera 48MP 1/2-inch CMOS, Video 4K 60fps HDR, 34 Menit Waktu Terbang per Baterai, 3x Intelligent Flight Batteries, Tas Bahu & Filter ND',
                'condition' => 'baik',
                'status' => 'tersedia',
                'daily_rental_fee' => 50000,
                'image_path' => 'equipments/eq_drn_001.png',
            ],
            [
                'id' => 6,
                'category_id' => 3,
                'asset_code' => 'EQ-DRN-003',
                'name' => 'FPV Racing Drone BetaFPV Cetus Pro',
                'specification' => 'Brushless Quadcopter, LiteRadio2 SE Transmitter, VR02 FPV Goggles, 2x BT2.0 450mAh 1S Batteries, 3 Flight Modes',
                'condition' => 'baik',
                'status' => 'tersedia',
                'daily_rental_fee' => 25000,
                'image_path' => 'equipments/eq_drn_003.png',
            ],
        ];

        foreach ($equipments as $equipment) {
            Equipment::updateOrCreate(['id' => $equipment['id']], $equipment);
        }
    }
}
