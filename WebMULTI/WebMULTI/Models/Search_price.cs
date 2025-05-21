using System;
using System.Collections.Generic;
using System.Linq;
using System.Net.Http.Headers;
using System.Web;

namespace WebMULTI.Models
{
    public class Search_price
    {
        // nếu sản phẩm đã có trong database thì truy xuất trực tiếp từ database
       private webMultiDataContext db=new webMultiDataContext();
        public List<SanPham> GetAllProducts()
        {
            return db.SanPhams.ToList();
        }
        public List<SanPham> FilterProductsByPriceRange(double min, double max)
        {
            return db.SanPhams.Where(p=>p.GiaSP>= min && p.GiaSP<= max).ToList();
        }
    }
}