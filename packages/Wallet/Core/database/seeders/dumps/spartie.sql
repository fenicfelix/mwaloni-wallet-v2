SET foreign_key_checks = 0;
TRUNCATE TABLE `sp_model_has_permissions`;
TRUNCATE TABLE `sp_model_has_roles`;
TRUNCATE TABLE `sp_permissions`;
TRUNCATE TABLE `sp_role_has_permissions`;
TRUNCATE TABLE `sp_roles`;
SET foreign_key_checks = 1;

INSERT INTO `sp_permissions` (`name`, `guard_name`, `created_at`, `updated_at`) VALUES
('analytics-list','web','2023-06-02 08:06:38','2023-06-02 08:06:38'),
('account-list','web','2023-06-02 08:06:38','2023-06-02 08:06:38'),
('account-create','web','2023-06-02 08:06:38','2023-06-02 08:06:38'),
('account-edit','web','2023-06-02 08:06:38','2023-06-02 08:06:38'),
('account-delete','web','2023-06-02 08:06:38','2023-06-02 08:06:38'),
('account-cashout','web','2023-06-02 08:06:38','2023-06-02 08:06:38'),
('account-fetch-balance','web','2023-06-02 08:06:38','2023-06-02 08:06:38'),
('account-activate','web','2023-06-02 08:06:38','2023-06-02 08:06:38'),
('transaction-list','web','2023-06-02 08:06:38','2023-06-02 08:06:38'),
('transaction-create','web','2023-06-02 08:06:38','2023-06-02 08:06:38'),
('transaction-edit','web','2023-06-02 08:06:38','2023-06-02 08:06:38'),
('transaction-delete','web','2023-06-02 08:06:38','2023-06-02 08:06:38'),
('transaction-reverse','web', '2023-06-02 15:29:47','2023-06-02 15:29:47'),
('transaction-retry','web', '2023-06-02 15:29:47','2023-06-02 15:29:47'),
('transaction-check-status','web', '2023-06-02 15:29:47','2023-06-02 15:29:47'),
('transaction-offline','web', '2023-06-02 15:29:47','2023-06-02 15:29:47'),
('client-list','web','2023-06-02 08:06:38','2023-06-02 08:06:38'),
('client-create','web','2023-06-02 08:06:38','2023-06-02 08:06:38'),
('client-edit','web','2023-06-02 08:06:38','2023-06-02 08:06:38'),
('client-delete','web','2023-06-02 08:06:39','2023-06-02 08:06:39'),
('service-list','web','2023-06-02 08:06:39','2023-06-02 08:06:39'),
('service-create','web','2023-06-02 08:06:39','2023-06-02 08:06:39'),
('service-edit','web','2023-06-02 08:06:39','2023-06-02 08:06:39'),
('service-delete','web','2023-06-02 08:06:39','2023-06-02 08:06:39'),
('service-withdraw','web','2023-06-02 08:06:39','2023-06-02 08:06:39'),
('message-list','web','2023-06-02 08:06:39','2023-06-02 08:06:39'),
('user-list','web','2023-06-02 08:06:39','2023-06-02 08:06:39'),
('user-create','web','2023-06-02 08:06:39','2023-06-02 08:06:39'),
('user-edit','web','2023-06-02 08:06:39','2023-06-02 08:06:39'),
('user-delete','web','2023-06-02 08:06:39','2023-06-02 08:06:39'),
('technical-manage','web','2023-06-02 08:06:39','2023-06-02 08:06:39'),
('dashboard-list','web','2023-06-02 08:17:37','2023-06-02 08:17:37'),
('account-manage','web','2023-06-02 15:29:46','2023-06-02 15:29:46'),
('service-manage','web','2023-06-02 15:29:46','2023-06-02 15:29:46'),
('compose-message','web','2023-06-02 15:29:47','2023-06-02 15:29:47');

INSERT INTO `sp_roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1,'Technical','web','2023-06-02 09:08:05','2023-06-02 09:08:05'),
(2,'Admin','web','2023-06-02 09:08:59','2023-06-02 09:08:59'),
(3,'Account Manager','web','2023-06-02 09:10:28','2023-06-02 09:10:28');

INSERT INTO `sp_model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1,'App\\Models\\User',1);