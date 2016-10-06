/*
 * Copyright (C) 2016 Eric Sprangers
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

(function ($) {
    "use strict";
    $(document).ready(function () {
        $(".thickbox").each(function () {
            var width = $(window).width();
            var height = $(window).height();
            if (width > 700) {
                height = Math.floor(height * 0.70);
                width = Math.floor(width * 0.9);
            } else {
                height = Math.floor(height * 0.9);
                width = Math.floor(width * 0.9);
            }
            var link = $(this).attr("href");
            $(this).attr("href", link + "&width=" + width + "&height=" + height);
        });
    });
})(jQuery);
