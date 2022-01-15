import {Account, User} from './accounts_pb'
import {AccountServicePromiseClient} from './accounts_grpc_web_pb'

export default class {
  constructor () {
    this.client = new AccountServicePromiseClient('http://localhost:2181', null, {})
  }

  async create () {
    const req = new User()
    req.setEmail('me@gustavohenrique.com')
    req.setPassword('verystrongpassword')
    try {
      const res = await this.client.create(req, {})
      return res.getEmail()
    } catch (err) {
      console.error(err.message)
      throw err
    }
  }
}
