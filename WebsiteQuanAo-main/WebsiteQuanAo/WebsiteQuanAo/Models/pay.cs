using System;
using System.Collections.Generic;
using System.ComponentModel.DataAnnotations;
using System.Linq;
using System.Web;

namespace WebsiteQuanAo.Models
{
    public class pay
    {
        [Required(ErrorMessage = "Vui lòng nhập họ tên.")]
        public string Fullname { get; set; }
        [Required(ErrorMessage = "Vui lòng nhập số điện thoại.")]
        public int sodienthoai { get; set; }
        [Required(ErrorMessage = "Vui lòng nhập địa chỉ của bạn")]
        public string phuong { get; set; }
        public string huỵen { get; set; }
        public string tinh { get; set; }
        [Required(ErrorMessage = "Vui lòng nhập địa chỉ của bạn")]
        public string diachi { get; set; }
        [Required(ErrorMessage = "Vui lòng nhập địa chỉ Email")]
        public string dichiEmail { get; set; }
        [Required(ErrorMessage = "Vui lòng nhập ghi chú")]
        public string ghichu { get; set; }
        public string phuongthuc { get; set; }
    }
}