export default (data) => ({
    id: data ? data.id : null,
    createdAt: data ? data.createdAt : null,
    total: data ? data.total : 0,
    status: data ? data.total : null,
    orderData: data ? data.orderData : [],
    dispatches: data ? data.dispatches : [],
    payments: data ? data.payments : [],
})
