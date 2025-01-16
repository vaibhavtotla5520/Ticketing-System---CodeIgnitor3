<?php
defined('BASEPATH') OR exit('No direct script access allowed');


$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

$route['default_controller'] = 'BaseController/index';
$route['login'] = 'BaseController/loginAction';
$route['logout'] = 'BaseController/logoutUser';
$route['change-role'] = 'BaseController/changeUserRole';
$route['add-user'] = 'BaseController/addUser';
$route['add_user_action']['post'] = 'BaseController/addUserAction';
$route['edit-user'] = 'BaseController/editUser';
$route['edit-user/(:num)'] = 'BaseController/editUser/$1';
$route['get-user-details']['post'] = 'BaseController/getUserDetails';
$route['update-user']['post'] = 'BaseController/updateUser';

$route['create-ticket'] = 'TicketController/createTicket';
$route['generate-ticket']['post'] = 'TicketController/generateTicket';
$route['add-ticket-reply']['post'] = 'TicketController/replyTicket';
$route['dashboard'] = 'TicketController/dashboard';
$route['dashboard/(:num)'] = 'TicketController/dashboard/$1';
$route['fetch-tickets'] = 'TicketController/getTickets';
$route['ticket-detail'] = 'TicketController/ticketDeatil';
$route['update-status']['post'] = 'TicketController/updateStatus';
