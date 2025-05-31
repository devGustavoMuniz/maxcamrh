<?php

namespace App\Enums;

enum UserRole: string
{
  case ADMIN = 'admin';
  case FRANCHISE = 'franchise';
  case CLIENT = 'client';
  case COLLABORATOR = 'collaborator';
}
