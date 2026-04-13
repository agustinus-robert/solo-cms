/*
 Navicat Premium Data Transfer

 Source Server         : Postgre-local
 Source Server Type    : PostgreSQL
 Source Server Version : 140019 (140019)
 Source Host           : localhost:5432
 Source Catalog        : solocms
 Source Schema         : public

 Target Server Type    : PostgreSQL
 Target Server Version : 140019 (140019)
 File Encoding         : 65001

 Date: 17/03/2026 04:54:58
*/


-- ----------------------------
-- Records of app_failed_jobs
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of app_jobs
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of app_permissions
-- ----------------------------
BEGIN;
INSERT INTO "app_permissions" ("id", "key", "name", "module", "model", "description", "guard_name") VALUES (1, 'read-users', 'Read users', 'Account', 'User', 'Allow user to read users', 'web'), (2, 'write-users', 'Write users', 'Account', 'User', 'Allow user to write users', 'web'), (3, 'delete-users', 'Delete users', 'Account', 'User', 'Allow user to delete users', 'web'), (4, 'cross-login-users', 'Cross-login users', 'Account', 'User', 'Allow user to cross-login users', 'web'), (5, 'read-user-logs', 'Read user logs', 'Account', 'UserLog', 'Allow user to read user logs', 'web'), (6, 'delete-user-logs', 'Delete user logs', 'Account', 'UserLog', 'Allow user to delete user logs', 'web');
COMMIT;

-- ----------------------------
-- Records of app_role_permissions
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of app_roles
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of app_settings
-- ----------------------------
BEGIN;
INSERT INTO "app_settings" ("id", "key", "value", "type", "created_at", "updated_at") VALUES (1, 'app_short_name', 'Solo CMS', 'string', '2026-03-14 22:31:36+07', '2026-03-14 22:31:36+07'), (2, 'app_name', 'Solo CMS', 'string', '2026-03-14 22:31:36+07', '2026-03-14 22:31:36+07'), (3, 'app_long_name', 'Sistem POS, E-commerce, Compro', 'string', '2026-03-14 22:31:36+07', '2026-03-14 22:31:36+07'), (4, 'meta_author', 'backend2', 'string', '2026-03-14 22:31:36+07', '2026-03-14 22:31:36+07'), (5, 'meta_keywords', 'website, umkm, e-commerce', 'string', '2026-03-14 22:31:36+07', '2026-03-14 22:31:36+07'), (6, 'meta_image', '/img/logo/logo-icon-sq-512.png', 'string', '2026-03-14 22:31:36+07', '2026-03-14 22:31:36+07'), (7, 'meta_description', 'http://localhost', 'string', '2026-03-14 22:31:36+07', '2026-03-14 22:31:36+07'), (8, 'theme', 'electro', 'string', '2026-03-14 22:31:36+07', '2026-03-14 22:31:36+07');
COMMIT;

-- ----------------------------
-- Records of brand
-- ----------------------------
BEGIN;
INSERT INTO "ref_brands" ("id", "code", "name", "slug", "description", "location", "image_name", "is_shortcut", "created_by", "deleted_by", "updated_by", "deleted_at", "created_at", "updated_at") VALUES (2, '0641363212', 'Apple', 'apple', 'segala produk tentang apple', NULL, NULL, NULL, 10000003, NULL, 10000003, NULL, '2026-03-15 21:49:16+07', '2026-03-15 21:49:16+07'), (3, '3901186248', 'Apple', 'apple', 'Segala tentang apple', NULL, NULL, NULL, 10000003, NULL, 10000003, NULL, '2026-03-15 21:51:35+07', '2026-03-15 21:51:35+07');
COMMIT;

-- ----------------------------
-- Records of cash_register
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of cash_register_log
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of cash_register_topup
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of category
-- ----------------------------
BEGIN;
INSERT INTO "ref_categories" ("id", "code", "name", "slug", "description", "location", "image_name", "parent_id", "created_by", "deleted_by", "updated_by", "deleted_at", "created_at", "updated_at") VALUES (2, '6876518982', 'Gadget', 'gadget', 'gadget', 'dummy/', 'no-pictures.png', NULL, NULL, NULL, NULL, NULL, '2026-03-15 21:55:04+07', '2026-03-15 21:55:04+07'), (3, '0357719201', 'Monitor', 'monitor', 'monitor', 'dummy/', 'no-pictures.png', NULL, NULL, NULL, NULL, NULL, '2026-03-15 21:55:23+07', '2026-03-15 21:55:23+07'), (4, '6519457746', 'Laptop', 'laptop', 'laptop kategori', 'dummy/', 'no-pictures.png', NULL, NULL, NULL, NULL, NULL, '2026-03-15 22:14:34+07', '2026-03-15 22:14:34+07');
COMMIT;

-- ----------------------------
-- Records of central_tenants
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of central_tenants_metas
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of cmp_role_permissions
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of cmp_role_users
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of cmp_roles
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of cms_category
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of cms_comments
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of cms_live_editor_access
-- ----------------------------
BEGIN;
INSERT INTO "cms_live_editor_access" ("id", "user_id", "status", "deleted_at", "created_at", "updated_at") VALUES (1, 10000002, 1, NULL, '2026-03-15 13:19:49', '2026-03-15 13:19:55');
COMMIT;

-- ----------------------------
-- Records of cms_menu
-- ----------------------------
BEGIN;
INSERT INTO "cms_menu" ("id", "title", "icon", "slug", "type", "meta", "custom_links", "post_code", "taxonomy_code", "image_code", "woocomerce_code", "add", "edit", "delete", "album", "video", "deleted_at", "created_at", "updated_at", "meta_keyword") VALUES (1859659598746649, '{"id":"Home"}', NULL, '{"id":"home"}', 1, '""', '', '[]', '[]', '{"size_image":"","orientation_image":""}', '-', 1, 1, 1, 1, NULL, NULL, '2026-03-15 00:33:16', '2026-03-15 00:33:16', '[]'), (1859690530369920, '{"id":"offering"}', NULL, '{"id":"offering"}', 2, '""', '', '{"field_1859690400102360":{"ft1859690400102360":{"id":"Harga Awal"},"fy1859690400102360":"raw_text","v1859690400102360":"not_required"},"field_1859690441224660":{"ft1859690441224660":{"id":"Harga Potongan"},"fy1859690441224660":"raw_text","v1859690441224660":"not_required"},"field_1859690489103292":{"ft1859690489103292":{"id":"Hemat sampai dengan"},"fy1859690489103292":"raw_text","v1859690489103292":"not_required"},"field_1859690505356885":{"ft1859690505356885":{"id":"Kategori"},"fy1859690505356885":"raw_text","v1859690505356885":"not_required"}}', '[]', '{"size_image":"","orientation_image":""}', '-', 1, 1, 1, 1, NULL, NULL, '2026-03-15 08:44:55', '2026-03-15 08:44:55', '[]'), (1859690724124090, '{"id":"Layanan"}', NULL, '{"id":"layanan"}', 2, '""', '', '{"field_1859690688567085":{"ft1859690688567085":{"id":"Keterangan"},"fy1859690688567085":"textarea","v1859690688567085":"not_required"},"field_1859690705478560":{"ft1859690705478560":{"id":"icon"},"fy1859690705478560":"raw_text","v1859690705478560":"not_required"}}', '[]', '{"size_image":"","orientation_image":""}', '-', 1, 1, 1, 1, NULL, NULL, '2026-03-15 08:48:00', '2026-03-15 08:48:00', '[]'), (1859690864013928, '{"id":"Promotion"}', NULL, '{"id":"promotion"}', 2, '""', '', '{"field_1859690768935364":{"ft1859690768935364":{"id":"keterangan"},"fy1859690768935364":"raw_text","v1859690768935364":"not_required"},"field_1859690826149726":{"ft1859690826149726":{"id":"persen"},"fy1859690826149726":"raw_text","v1859690826149726":"not_required"},"field_1859690840257175":{"ft1859690840257175":{"id":"keterangan persen"},"fy1859690840257175":"raw_text","v1859690840257175":"not_required"}}', '[]', '{"size_image":"","orientation_image":""}', '-', 1, 1, 1, 1, NULL, NULL, '2026-03-15 08:50:13', '2026-03-15 08:50:13', '[]'), (1859690973687910, '{"id":"Produk"}', NULL, '{"id":"produk"}', 2, '""', '', '[]', '[]', '{"size_image":"","orientation_image":""}', '-', 1, 1, 1, 1, NULL, NULL, '2026-03-15 08:51:58', '2026-03-15 08:51:58', '[]'), (1859691162395008, '{"id":"Promosi"}', NULL, '{"id":"promosi"}', 2, '""', '', '{"field_1859691123947166":{"ft1859691123947166":{"id":"Harga"},"fy1859691123947166":"raw_text","v1859691123947166":"not_required"},"field_1859691144404178":{"ft1859691144404178":{"id":"Link"},"fy1859691144404178":"raw_text","v1859691144404178":"not_required"}}', '[]', '{"size_image":"","orientation_image":""}', '-', 1, 1, 1, 1, NULL, NULL, '2026-03-15 08:54:58', '2026-03-15 08:54:58', '[]'), (1859691249839952, '{"id":"Summary product"}', NULL, '{"id":"summary-product"}', 2, '""', '', '{"field_1859691236901684":{"ft1859691236901684":{"id":"keterangan"},"fy1859691236901684":"raw_text","v1859691236901684":"not_required"}}', '[]', '{"size_image":"","orientation_image":""}', '-', 1, 1, 1, 1, NULL, NULL, '2026-03-15 08:56:21', '2026-03-15 08:56:21', '[]'), (1859691320757279, '{"id":"Summary Best Seller"}', NULL, '{"id":"summary-best-seller"}', 2, '""', '', '{"field_1859691300160001":{"ft1859691300160001":{"id":"keterangan"},"fy1859691300160001":"textarea","v1859691300160001":"not_required"}}', '[]', '{"size_image":"","orientation_image":""}', '-', 1, 1, 1, 1, NULL, NULL, '2026-03-15 08:57:29', '2026-03-15 08:57:29', '[]'), (1859690265115931, '{"id":"Slider"}', NULL, '{"id":"slider"}', 2, '""', '', '{"field_1859690173161521":{"ft1859690173161521":{"id":"keterangan atas"},"fy1859690173161521":"raw_text","v1859690173161521":"not_required"},"field_1859690206867776":{"ft1859690206867776":{"id":"keterangan tengah"},"fy1859690206867776":"raw_text","v1859690206867776":"not_required"},"field_1859692991409063":{"ft1859692991409063":{"id":"link"},"fy1859692991409063":"raw_text","v1859692991409063":"not_required"}}', '[]', '{"size_image":"","orientation_image":""}', '-', 1, 1, 1, 1, NULL, NULL, '2026-03-15 08:40:42', '2026-03-15 09:24:18', '[]');
COMMIT;

-- ----------------------------
-- Records of cms_menu_category
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of cms_menu_order
-- ----------------------------
BEGIN;
INSERT INTO "cms_menu_order" ("id", "menu_text", "deleted_at", "created_at", "updated_at") VALUES (1, '[{"id":1859659598746649,"children":[{"id":1859690265115931},{"id":1859690530369920},{"id":1859690724124090},{"id":1859690864013928},{"id":1859691249839952},{"id":1859691320757279}]},{"id":1859690973687910,"children":[{"id":1859691162395008}]}]', NULL, NULL, '2026-03-15 11:21:13');
COMMIT;

-- ----------------------------
-- Records of cms_menu_related
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of cms_post
-- ----------------------------
BEGIN;
INSERT INTO "cms_post" ("id", "menu_id", "content", "tags", "location", "image", "status", "alt_image", "deleted_at", "created_at", "updated_at", "created_by", "updated_by", "deleted_by") VALUES (2, 1859690265115931, '{"id":{"post0":"Diskon hingga 50%","post1":"Diskon pembelian laptop dan Smartphone","post2":"#","slug":"produk-smart-laptop","title":"Produk Smart Laptop","media_description":"","meta_description":""}}', NULL, 'image_posting/1859690265115931/69b6195e23723', 'QlRkWmFfjdpfQGXAe556JIxz14C3iB-metaY2Fyb3VzZWwtMS5wbmc=-.png', 2, NULL, NULL, '2026-03-15 09:28:46', '2026-03-15 09:28:46', 10000002, 10000002, NULL), (4, 1859690530369920, '{"id":{"post0":"5000000","post1":"2500000","post2":"Hemat 50%","post3":"smartphone","slug":"penawaran-produk-apple","title":"Penawaran Produk Apple","media_description":"","meta_description":""}}', NULL, 'image_posting/1859690530369920/69b632703a286', 'gSTQPAkurPQ5bp76HgQLU73BCyQ2Ei-metaaGFwcHktYXNpYW4tYnVzaW5lc3Mtd29tYW4taGFuZC02MDBudy0yNjkwODI1OTM1LmpwZw==-.jpg', 2, NULL, NULL, '2026-03-15 11:15:44', '2026-03-15 11:15:44', 10000002, 10000002, NULL), (5, 1859690724124090, '{"id":{"post0":"30 days money back guarantee!","post1":"fa fa-sync-alt fa-2x text-primary","slug":"free-return","title":"Free Return","media_description":"","meta_description":""}}', NULL, NULL, NULL, 2, NULL, NULL, '2026-03-15 11:16:48', '2026-03-15 11:16:48', 10000002, 10000002, NULL), (6, 1859690724124090, '{"id":{"post0":"Free shipping on all order","post1":"fab fa-telegram-plane fa-2x text-primary","slug":"free-shipping","title":"Free Shipping","media_description":"","meta_description":""}}', NULL, NULL, NULL, 2, NULL, NULL, '2026-03-15 11:17:21', '2026-03-15 11:17:21', 10000002, 10000002, NULL), (7, 1859690724124090, '{"id":{"post0":"We support online 24 hrs a day","post1":"fas fa-life-ring fa-2x text-primary","slug":"support-24\/7","title":"Support 24\/7","media_description":"","meta_description":""}}', NULL, NULL, NULL, 2, NULL, NULL, '2026-03-15 11:17:54', '2026-03-15 11:17:54', 10000002, 10000002, NULL), (8, 1859690724124090, '{"id":{"post0":"Recieve gift all over oder $50","post1":"fas fa-credit-card fa-2x text-primary","slug":"receive-gift-card","title":"Receive Gift Card","media_description":"","meta_description":""}}', NULL, NULL, NULL, 2, NULL, NULL, '2026-03-15 11:18:27', '2026-03-15 11:18:27', 10000002, 10000002, NULL), (9, 1859690724124090, '{"id":{"post0":"We Value Your Security","post1":"fas fa-lock fa-2x text-primary","slug":"secure-payment","title":"Secure Payment","media_description":"","meta_description":""}}', NULL, NULL, NULL, 2, NULL, NULL, '2026-03-15 11:19:03', '2026-03-15 11:19:03', 10000002, 10000002, NULL), (10, 1859690724124090, '{"id":{"post0":"Free return products in 30 days","post1":"fas fa-blog fa-2x text-primary","slug":"online-service","title":"Online Service","media_description":"","meta_description":""}}', NULL, NULL, NULL, 2, NULL, NULL, '2026-03-15 11:19:40', '2026-03-15 11:19:40', 10000002, 10000002, NULL), (12, 1859690864013928, '{"id":{"post0":"Find The Best Whatches for You!","post1":"20%","post2":"Off","slug":"smart-watch","title":"Smart Watch","media_description":"","meta_description":""}}', NULL, 'image_posting/1859690864013928/69b6343094e3e', 'HcGinKSFo9crkvr73KdlvSmIVTOq9v-metacHJvZHVjdC0yLnBuZw==-.png', 2, NULL, NULL, '2026-03-15 11:23:12', '2026-03-15 11:23:12', 10000002, 10000002, NULL), (11, 1859690864013928, '{"id":{"post0":"Find The Best Camera for You!","post1":"40%","post2":"Off","slug":"smart-camera","title":"Smart Camera","media_description":"","meta_description":""}}', NULL, 'image_posting/1859690864013928/69b634d33a4ab', 'glHLl7cX8u3PUTPZXQBzHJNthggHEx-metacHJvZHVjdC0xLnBuZw==-.png', 2, NULL, NULL, '2026-03-15 11:22:10', '2026-03-15 11:25:55', 10000002, 10000002, NULL), (13, 1859691249839952, '{"id":{"post0":"All Product Items","slug":"products","title":"Products","media_description":"","meta_description":""}}', NULL, NULL, NULL, 2, NULL, NULL, '2026-03-15 11:26:38', '2026-03-15 11:26:38', 10000002, 10000002, NULL), (14, 1859691320757279, '{"id":{"post0":"Lorem ipsum dolor sit amet consectetur adipisicing elit. Modi, asperiores ducimus sint quos tempore officia similique quia? Libero, pariatur consectetur?","slug":"bestseller-products","title":"Bestseller Products","media_description":"","meta_description":""}}', NULL, NULL, NULL, 2, NULL, NULL, '2026-03-15 11:37:36', '2026-03-15 11:37:36', 10000002, 10000002, NULL), (3, 1859690265115931, '{"id":{"title":"Promo Diskon Laptop","post0":"Promo Laptop","post1":"Nikmati promo hari senin","post2":"#","slug":"promo-diskon-laptop"}}', NULL, 'image_posting/1859690265115931/69b62e624240d', 'NJ8eGNjQvCYPcMGj1xhudZmIiOIXqL-metaY2Fyb3VzZWwtMi5wbmc=-.png', 2, NULL, NULL, '2026-03-15 10:58:26', '2026-03-15 17:19:57', 10000002, 10000002, NULL);
COMMIT;

-- ----------------------------
-- Records of cms_post_has_category
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of cms_post_image
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of cms_post_image_has_category
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of cms_post_meta
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of cms_post_site_configuration
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of cms_post_video
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of cms_schedule_post
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of cms_tags
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of config
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of customer
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of desk
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of doc_signatures
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of docs
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of failed_jobs
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of job_progress
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of migrations
-- ----------------------------
BEGIN;
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (1, '0000_00_00_000000_create_app_table', 1), (2, '2014_10_12_000000_create_users_table', 1), (3, '2014_10_12_200000_add_two_factor_columns_to_users_table', 1), (4, '2019_08_19_000000_create_failed_jobs_table', 1), (5, '2019_08_20_000000_create_references_table', 1), (6, '2019_08_21_000000_create_config_table', 1), (7, '2020_01_10_054134_create_rbac_table', 1), (8, '2020_05_21_100000_create_teams_table', 1), (9, '2020_05_21_200000_create_team_user_table', 1), (10, '2020_05_21_300000_create_team_invitations_table', 1), (11, '2022_00_00_000000_create_account_table', 1), (12, '2022_01_00_000000_create_company_table', 1), (13, '2022_01_00_000000_create_oauth_table', 1), (14, '2022_02_00_000000_create_doc_table', 1), (15, '2024_00_00_000001_create_signupuser_table', 1), (16, '2024_01_00_000000_create_brand_table', 1), (17, '2024_02_00_000000_create_category_table', 1), (18, '2024_02_06_000000_create_ref_table', 1), (19, '2024_03_00_000000_create_unit_table', 1), (20, '2024_04_00_000000_create_tax_rate_table', 1), (21, '2024_04_01_000000_create_product_table', 1), (22, '2024_05_00_000000_create_warehouse_table', 1), (23, '2024_06_00_000000_create_customer_table', 1), (24, '2024_07_00_000000_create_supplier_table', 1), (25, '2025_00_00_000001_create_category_table', 1), (26, '2025_00_00_000001_create_menu_table', 1), (27, '2025_00_00_000001_create_posts_table', 1), (28, '2025_00_00_000001_create_tags_table', 1), (29, '2025_00_11_000001_create_menu_related_table', 1), (30, '2025_03_15_171037_create_sessions_table', 1), (31, '2025_04_04_000000_create_return_table', 1), (32, '2025_04_05_000000_create_purchase_table', 1), (33, '2025_04_06_000000_create_sale_table', 1), (34, '2025_04_07_000000_create_product_stock_table', 1), (35, '2025_05_08_000000_create_adjustment_product', 1), (36, '2025_09_08_000001_create_tenant_table', 1), (37, '2025_11_24_154806_create_job_progress_table', 1), (38, '2025_15_03_000000_create_outlet_table', 1), (39, '2025_16_00_000000_create_desk_table', 1), (40, '2025_17_00_000000_create_shift_table', 1), (41, '2025_18_00_000000_create_cash_table', 1), (42, '2025_19_08_000000_create_supplier_schedule_table', 1), (43, '2025_20_09_000000_create_product_quotation_table', 1), (44, '2026_00_00_000001_create_live_editor_access_table', 1);
COMMIT;

-- ----------------------------
-- Records of notifications
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of oauth_access_tokens
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of oauth_auth_codes
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of oauth_clients
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of oauth_personal_access_clients
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of oauth_refresh_tokens
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of outlet
-- ----------------------------
BEGIN;
INSERT INTO "outlets" ("id", "admin_id", "code", "name", "description", "location", "image_name", "created_by", "deleted_by", "updated_by", "deleted_at", "created_at", "updated_at") VALUES (2, 10000003, '2046993528', 'Tokoku', 'dekat toko', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-15 21:51:09+07', '2026-03-15 21:51:09+07');
COMMIT;

-- ----------------------------
-- Records of outlet_brand
-- ----------------------------
BEGIN;
INSERT INTO "outlet_brands" ("id", "outlet_id", "brand_id", "created_by", "deleted_by", "updated_by", "deleted_at", "created_at", "updated_at") VALUES (1, 2, 3, NULL, NULL, NULL, NULL, NULL, NULL);
COMMIT;

-- ----------------------------
-- Records of outlet_category
-- ----------------------------
BEGIN;
INSERT INTO "outlet_categories" ("id", "outlet_id", "category_id", "created_by", "deleted_by", "updated_by", "deleted_at", "created_at", "updated_at") VALUES (1, 2, 2, NULL, NULL, NULL, NULL, NULL, NULL), (2, 2, 3, NULL, NULL, NULL, NULL, NULL, NULL), (3, 2, 4, NULL, NULL, NULL, NULL, NULL, NULL);
COMMIT;

-- ----------------------------
-- Records of outlet_product
-- ----------------------------
BEGIN;
INSERT INTO "outlet_products" ("id", "outlet_id", "product_id", "created_by", "deleted_by", "updated_by", "deleted_at", "created_at", "updated_at") VALUES (1, 2, 1, NULL, NULL, NULL, NULL, NULL, NULL), (2, 2, 2, NULL, NULL, NULL, NULL, NULL, NULL), (3, 2, 3, NULL, NULL, NULL, NULL, NULL, NULL), (4, 2, 4, NULL, NULL, NULL, NULL, NULL, NULL), (5, 2, 5, NULL, NULL, NULL, NULL, NULL, NULL), (6, 2, 6, NULL, NULL, NULL, NULL, NULL, NULL);
COMMIT;

-- ----------------------------
-- Records of outlet_product_adjustment
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of outlet_product_quotation
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of outlet_product_stock
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of outlet_purchase
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of outlet_return
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of outlet_sale
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of outlet_supplier
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of outlet_tax_rate
-- ----------------------------
BEGIN;
INSERT INTO "outlet_tax_rates" ("id", "outlet_id", "tax_id", "created_by", "deleted_by", "updated_by", "deleted_at", "created_at", "updated_at") VALUES (1, 2, 2, NULL, NULL, NULL, NULL, NULL, NULL);
COMMIT;

-- ----------------------------
-- Records of outlet_unit
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of permissions
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of product
-- ----------------------------
BEGIN;
INSERT INTO "products" ("id", "type", "alert_qty", "code", "name", "barcode", "brand_id", "category_id", "sub_category_id", "unit_id", "tax_rate_id", "price", "description", "location", "image_name", "wholesale", "weight", "created_by", "deleted_by", "updated_by", "deleted_at", "created_at", "updated_at") VALUES (1, 1, NULL, '4836354248', 'Iphone 4', '1', 3, 2, NULL, 1, 2, 3000000.00, NULL, 'file_product/69b6cc286e15c', 'O9iLhbiNRGcCowpq1KF3UXFisQIbfn-metaaXBob25lNC5qcGc=-.jpg', 2000000.00, 1, 10000003, NULL, 10000003, NULL, '2026-03-15 22:11:36+07', '2026-03-15 22:11:36+07'), (2, 1, NULL, '3006239453', 'iphone xr', '1', 3, 2, NULL, 1, 2, 5000000.00, NULL, 'file_product/69b6cc6f3cdbc', 'TmxWfrKJCQfit8R8G1zFAABnqm0Bq4-metaaXBob25lLXhyLnBuZw==-.png', 4000000.00, 1, 10000003, NULL, 10000003, NULL, '2026-03-15 22:12:47+07', '2026-03-15 22:12:47+07'), (3, 1, NULL, '8443647235', 'Iphone 13 Pro Max', '1', 3, 2, NULL, 1, 2, 7000000.00, NULL, 'file_product/69b6ccc19cfe6', 'R3KfDTJ6w0NtWu5BR6UKv6AKueBAsi-metaaXBob25lIDEzIHByb21heC5qcGc=-.jpg', 6000000.00, 1, 10000003, NULL, 10000003, NULL, '2026-03-15 22:14:09+07', '2026-03-15 22:14:09+07'), (4, 1, NULL, '6023140254', 'Macbook pro m1', '1', 3, 4, NULL, 1, 2, 15000000.00, NULL, 'file_product/69b6cd35979fb', '9R986l76inoZAIagIGhwFCwoQFWQA9-metabGFwdG9wIG1hY2Jvb2suanBn-.jpg', 12000000.00, 1, 10000003, NULL, 10000003, NULL, '2026-03-15 22:16:05+07', '2026-03-15 22:16:05+07'), (5, 1, NULL, '0130137840', 'Apple Watch', '1', 3, 2, NULL, 1, 2, 700000.00, NULL, 'file_product/69b6cda694448', 'YjiO2AOb1TZ3tB7z98tFqWD2XP4dYx-metaYXBwbGUgd2F0Y2gucG5n-.png', 400000.00, 1, 10000003, NULL, 10000003, NULL, '2026-03-15 22:17:58+07', '2026-03-15 22:17:58+07'), (6, 1, NULL, '7299687569', 'Imac', '1', 3, 3, NULL, 1, 2, 2000000.00, NULL, 'file_product/69b6cdf67c158', 'mOO8zJyyfjksVIuMrwR6t4ZDXc5vRe-metaaW1hYy5qcGc=-.jpg', 1000000.00, 1, 10000003, NULL, 10000003, NULL, '2026-03-15 22:19:18+07', '2026-03-15 22:19:18+07');
COMMIT;

BEGIN;

INSERT INTO "public"."product_master_variants"
("product_id", "product_variant", "created_by", "updated_by", "created_at", "updated_at")
VALUES
(1, '[{"qty": 0, "code": "3006239453", "name": "No Variant", "price": "3000000", "status": "active", "alert_qty": "5", "tier_1_id": null, "tier_2_id": null, "wholesale": "4000000", "deleted_at": null, "variant_type": "no_variant"}]', NULL, 10000003, '2026-04-13 21:05:00+07', '2026-04-13 21:05:00+07'),
(2, '[{"qty": 0, "code": "3006239453", "name": "No Variant", "price": "5000000", "status": "active", "alert_qty": "5", "tier_1_id": null, "tier_2_id": null, "wholesale": "4000000", "deleted_at": null, "variant_type": "no_variant"}]', NULL, 10000003, '2026-04-13 21:05:00+07', '2026-04-13 21:05:00+07'),
(3, '[{"qty": 0, "code": "8443647235", "name": "No Variant", "price": "7000000", "status": "active", "alert_qty": "5", "tier_1_id": null, "tier_2_id": null, "wholesale": "6000000", "deleted_at": null, "variant_type": "no_variant"}]', NULL, 10000003, '2026-04-13 21:05:00+07', '2026-04-13 21:05:00+07'),
(4, '[{"qty": 0, "code": "6023140254", "name": "No Variant", "price": "15000000", "status": "active", "alert_qty": "5", "tier_1_id": null, "tier_2_id": null, "wholesale": "12000000", "deleted_at": null, "variant_type": "no_variant"}]', NULL, 10000003, '2026-04-13 21:05:00+07', '2026-04-13 21:05:00+07'),
(5, '[{"qty": 0, "code": "0130137840", "name": "No Variant", "price": "700000", "status": "active", "alert_qty": "5", "tier_1_id": null, "tier_2_id": null, "wholesale": "400000", "deleted_at": null, "variant_type": "no_variant"}]', NULL, 10000003, '2026-04-13 21:05:00+07', '2026-04-13 21:05:00+07'),
(6, '[{"qty": 0, "code": "7299687569", "name": "No Variant", "price": "2000000", "status": "active", "alert_qty": "5", "tier_1_id": null, "tier_2_id": null, "wholesale": "1000000", "deleted_at": null, "variant_type": "no_variant"}]', NULL, 10000003, '2026-04-13 21:05:00+07', '2026-04-13 21:05:00+07');
COMMIT;
-- ----------------------------
-- Records of product_adjustment
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of product_cart
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of product_gallery
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of product_label_variant
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of product_master_variant
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of product_quotation
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of product_quotation_items
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of product_stock
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of purchase
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of purchase_items
-- ----------------------------
BEGIN;
COMMIT;


-- ----------------------------
-- Records of return
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of return_items
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of role_permissions
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of roles
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of sale
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of sale_direct
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of sale_direct_chart
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of sale_direct_customer_desk
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of sale_direct_items
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of sale_items
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of schedule
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of sessions
-- ----------------------------
BEGIN;
INSERT INTO "sessions" ("id", "user_id", "ip_address", "user_agent", "payload", "last_activity") VALUES ('yBsWbuRK8CM63pLSudTcnQdJnnsWTpoy3JxcAftb', 10000002, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:148.0) Gecko/20100101 Firefox/148.0', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoia1dROGZDUjJ3cGNKekZaNVFXeW9zTFpLRU0wMW5zTDZSRDg0RDNKQiI7czoxMzoibGl2ZXdpcmUtdXJscyI7YTo2OntzOjg6InByZXZpb3VzIjtzOjM3OiJodHRwczovL3NsY21zLnRlc3QvaW1nL3Byb2R1Y3QtMTYucG5nIjtzOjE0OiJwcmV2aW91cy1yb3V0ZSI7czoxMzoid2ViOjp3ZWIucGFnZSI7czo3OiJjdXJyZW50IjtzOjM3OiJodHRwczovL3NsY21zLnRlc3QvaW1nL3Byb2R1Y3QtMTQucG5nIjtzOjEzOiJjdXJyZW50LXJvdXRlIjtzOjEzOiJ3ZWI6OndlYi5wYWdlIjtzOjc6Imhpc3RvcnkiO2E6MjA6e2k6NjI7czo2MzoiaHR0cHM6Ly9zbGNtcy50ZXN0L21hdGVyaWFsL2Nzcy9tYXRlcmlhbC1kYXNoYm9hcmQtZnVsbC5taW4uY3NzIjtpOjYzO3M6MzM6Imh0dHBzOi8vc2xjbXMudGVzdC9qcy9zY3JpcHRzMi5qcyI7aTo2NDtzOjE4OiJodHRwczovL3NsY21zLnRlc3QiO2k6NjU7czozNjoiaHR0cHM6Ly9zbGNtcy50ZXN0L2ltZy9wcm9kdWN0LTQucG5nIjtpOjY2O3M6Mzc6Imh0dHBzOi8vc2xjbXMudGVzdC9pbWcvcHJvZHVjdC0xMC5wbmciO2k6Njc7czozNzoiaHR0cHM6Ly9zbGNtcy50ZXN0L2ltZy9wcm9kdWN0LTEyLnBuZyI7aTo2ODtzOjM3OiJodHRwczovL3NsY21zLnRlc3QvaW1nL3Byb2R1Y3QtMTUucG5nIjtpOjY5O3M6Mzc6Imh0dHBzOi8vc2xjbXMudGVzdC9pbWcvcHJvZHVjdC0xNi5wbmciO2k6NzA7czozNjoiaHR0cHM6Ly9zbGNtcy50ZXN0Lz9saXZlX2VkaXRvcj10cnVlIjtpOjcxO3M6MzY6Imh0dHBzOi8vc2xjbXMudGVzdC9pbWcvcHJvZHVjdC00LnBuZyI7aTo3MjtzOjM2OiJodHRwczovL3NsY21zLnRlc3QvaW1nL3Byb2R1Y3QtOS5wbmciO2k6NzM7czozNzoiaHR0cHM6Ly9zbGNtcy50ZXN0L2ltZy9wcm9kdWN0LTEyLnBuZyI7aTo3NDtzOjM3OiJodHRwczovL3NsY21zLnRlc3QvaW1nL3Byb2R1Y3QtMTYucG5nIjtpOjc1O3M6MTg6Imh0dHBzOi8vc2xjbXMudGVzdCI7aTo3NjtzOjM2OiJodHRwczovL3NsY21zLnRlc3QvaW1nL3Byb2R1Y3QtNS5wbmciO2k6Nzc7czozNzoiaHR0cHM6Ly9zbGNtcy50ZXN0L2ltZy9wcm9kdWN0LTEwLnBuZyI7aTo3ODtzOjM3OiJodHRwczovL3NsY21zLnRlc3QvaW1nL3Byb2R1Y3QtMTEucG5nIjtpOjc5O3M6Mzc6Imh0dHBzOi8vc2xjbXMudGVzdC9pbWcvcHJvZHVjdC0xNS5wbmciO2k6ODA7czozNzoiaHR0cHM6Ly9zbGNtcy50ZXN0L2ltZy9wcm9kdWN0LTE2LnBuZyI7aTo4MTtzOjM3OiJodHRwczovL3NsY21zLnRlc3QvaW1nL3Byb2R1Y3QtMTQucG5nIjt9czoxMzoiaGlzdG9yeS1yb3V0ZSI7YToyMDp7aTo2MjtzOjEzOiJ3ZWI6OndlYi5wYWdlIjtpOjYzO3M6MTM6IndlYjo6d2ViLnBhZ2UiO2k6NjQ7TjtpOjY1O3M6MTM6IndlYjo6d2ViLnBhZ2UiO2k6NjY7czoxMzoid2ViOjp3ZWIucGFnZSI7aTo2NztzOjEzOiJ3ZWI6OndlYi5wYWdlIjtpOjY4O3M6MTM6IndlYjo6d2ViLnBhZ2UiO2k6Njk7czoxMzoid2ViOjp3ZWIucGFnZSI7aTo3MDtOO2k6NzE7czoxMzoid2ViOjp3ZWIucGFnZSI7aTo3MjtzOjEzOiJ3ZWI6OndlYi5wYWdlIjtpOjczO3M6MTM6IndlYjo6d2ViLnBhZ2UiO2k6NzQ7czoxMzoid2ViOjp3ZWIucGFnZSI7aTo3NTtOO2k6NzY7czoxMzoid2ViOjp3ZWIucGFnZSI7aTo3NztzOjEzOiJ3ZWI6OndlYi5wYWdlIjtpOjc4O3M6MTM6IndlYjo6d2ViLnBhZ2UiO2k6Nzk7czoxMzoid2ViOjp3ZWIucGFnZSI7aTo4MDtzOjEzOiJ3ZWI6OndlYi5wYWdlIjtpOjgxO3M6MTM6IndlYjo6d2ViLnBhZ2UiO319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHBzOi8vc2xjbXMudGVzdC9pbWcvcHJvZHVjdC0xNC5wbmciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxMDAwMDAwMjtzOjIyOiJQSFBERUJVR0JBUl9TVEFDS19EQVRBIjthOjA6e319', 1773691980);
COMMIT;

-- ----------------------------
-- Records of shift
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of shift_outlet_casier
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of signupuser
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of supplier_schedule
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of tax_rate
-- ----------------------------
BEGIN;
INSERT INTO "ref_tax_rates" ("id", "code", "name", "rate", "actived_on", "sale_active", "created_by", "deleted_by", "updated_by", "deleted_at", "created_at", "updated_at") VALUES (2, '7614684001', 'Pajak 11', 11.00, 1, 0, 10000003, NULL, 10000003, NULL, '2026-03-15 22:07:56+07', '2026-03-15 22:07:56+07');
COMMIT;

-- ----------------------------
-- Records of team_invitations
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of team_user
-- ----------------------------
BEGIN;
INSERT INTO "team_user" ("id", "team_id", "user_id", "role", "created_at", "updated_at") VALUES (1, 1, 10000001, NULL, '2026-03-14 22:31:36', '2026-03-14 22:31:36');
COMMIT;

-- ----------------------------
-- Records of teams
-- ----------------------------
BEGIN;
INSERT INTO "teams" ("id", "user_id", "name", "personal_team", "created_at", "updated_at") VALUES (1, 10000001, 'Admin System', 't', '2026-03-14 22:31:36', '2026-03-14 22:31:36');
COMMIT;

-- ----------------------------
-- Records of unit
-- ----------------------------
BEGIN;
INSERT INTO "ref_units" ("id", "code", "name", "created_by", "deleted_by", "updated_by", "deleted_at", "created_at", "updated_at") VALUES (1, '6242085774', 'PCS', NULL, NULL, NULL, NULL, '2025-08-14 14:19:13+07', '2025-08-14 14:19:13+07');
COMMIT;

-- ----------------------------
-- Records of user_achievements
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of user_address
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of user_appreciations
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of user_casier_outlet
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of user_disabilities
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of user_emails
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of user_father
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of user_foster
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of user_logs
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of user_metas
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of user_mother
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of user_organizations
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of user_password_resets
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of user_permissions
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of user_phones
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of user_profile
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of user_sessions
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Records of user_studies
-- ----------------------------
BEGIN;
COMMIT;


-- ----------------------------
-- Records of warehouse
-- ----------------------------
BEGIN;
COMMIT;
