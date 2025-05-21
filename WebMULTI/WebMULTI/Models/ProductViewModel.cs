using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;

namespace WebMULTI.Models
{
    public class ProductViewModel
    {
        public List<string> MauSac { get; set; }
        public int MaSP { get; set; }
        public string TenSP { get; set; }
        public string Mota { get; set; }
        public string AnhBia { get; set; }
        public int GiaSP { get; set; }
        public DateTime NgayNhap { get; set; }
        public int SoLuongBan { get; set; }
        public int SoLuongTon { get; set; }
        public int MaLoai { get; set; }
        public int MaDM { get; set; }
        public string Size { get; set; }
        public int idColor { get; set; }

    }
}