<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * This is VOOZANOO, a program. Contact voozanoo@epiconcept.fr, or   *
 * see http://www.voozanoo.net/ for more information.                *
 *                                                                   *
 * Copyright 2001-2013 Epiconcept                                    *
 *                                                                   *
 * This program is free software; you can redistribute it and/or     *
 * modify it under the terms of the GNU General Public License as    *
 * published by the Free Software Foundation - version 2             *
 *                                                                   *
 * This program is distributed in the hope that it will be useful,   *
 * but WITHOUT ANY WARRANTY; without even the implied warranty of    *
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the     *
 * GNU General Public License in file COPYING for more details.      *
 *                                                                   *
 * You should have received a copy of the GNU General Public         *
 * License along with this program; if not, write to the Free        *
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor,       *
 * Boston, MA 02111, USA.                                            *
 *                                                                   *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

defined('VOO4_APPBOOTSTRAPPER') || define( 'VOO4_APPBOOTSTRAPPER', getenv('VOO4_APPBOOTSTRAPPER') ? getenv('VOO4_APPBOOTSTRAPPER') : null);
require_once( VOO4_APPBOOTSTRAPPER );

Voo4_AppBootstrapper::runApplication( __DIR__ . '/..');

error_log(getenv(PATH_VOO4_EXTLIB));