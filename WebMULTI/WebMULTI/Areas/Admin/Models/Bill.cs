﻿using System;
using System.Collections.Generic;
using System.ComponentModel.DataAnnotations;
using System.Linq;
using System.Web;
using PagedList;
using PagedList.Mvc;
using WebMULTI.Models;

namespace WebMULTI.Areas.Admin.Models
{
    public class Bill
    {
        public DonHang DonHang { get; set; }
        public List<ChiTietDonHang> chiTietDonHangs { get; set; }
    }


}